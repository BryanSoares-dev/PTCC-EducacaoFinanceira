<?php

/**
 * POST /webhooks-asaas.php  (chamado pelo Asaas, não pelo navegador)
 * Configure em Asaas > Integrações > Webhooks:
 *   - URL: https://seusite.com.br/webhooks-asaas.php
 *   - Token de autenticação: o mesmo valor de ASAAS_WEBHOOK_TOKEN
 *   - Eventos: PAYMENT_RECEIVED, PAYMENT_CONFIRMED, PAYMENT_OVERDUE,
 *              PAYMENT_REFUNDED, PAYMENT_DELETED
 */

require_once __DIR__ . '/back-end/asaas.php';

function webhookResponder(int $status, string $texto)
{
    responderJson($status, ['status' => $texto]);
}

// 1) Autenticação: só aceita quem envia o token correto (falha segura).
$tokenEsperado = afdeConfig('ASAAS_WEBHOOK_TOKEN', '');
$tokenRecebido = $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ?? '';

if ($tokenEsperado === '' || !hash_equals($tokenEsperado, $tokenRecebido)) {
    error_log('[webhooks-asaas] Requisição recusada: token ausente/inválido.');
    webhookResponder(401, 'unauthorized');
}

// 2) Lê o conteúdo enviado pelo Asaas.
$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    webhookResponder(400, 'invalid_payload');
}

$evento    = (string) ($dados['event'] ?? '');
$pagamento = $dados['payment'] ?? [];
$paymentId = (string) ($pagamento['id'] ?? '');

// ID único do evento (se não vier, montamos um a partir do evento + pagamento + status).
$eventoId = (string) ($dados['id'] ?? ($evento . ':' . $paymentId . ':' . ($pagamento['status'] ?? '')));

if ($evento === '' || $paymentId === '') {
    webhookResponder(200, 'ignored'); // nada útil para processar
}

// 3) Mapa: evento do Asaas -> novo status do pedido.
$mapa = [
    'PAYMENT_RECEIVED'  => 'pago',
    'PAYMENT_CONFIRMED' => 'pago',
    'PAYMENT_OVERDUE'   => 'vencido',
    'PAYMENT_REFUNDED'  => 'reembolsado',
    'PAYMENT_DELETED'   => 'cancelado',
];

if (!isset($mapa[$evento])) {
    webhookResponder(200, 'ignored'); // evento que não nos interessa
}

$novoStatus = $mapa[$evento];
$pdo = afdeDb();

try {
    $pdo->beginTransaction();

    // 4) Idempotência: se o evento já foi registrado, não processa de novo.
    $st = $pdo->prepare('INSERT IGNORE INTO webhook_eventos (evento_id, tipo) VALUES (?, ?)');
    $st->execute([$eventoId, $evento]);

    if ($st->rowCount() === 0) {
        $pdo->rollBack();
        webhookResponder(200, 'already_processed');
    }

    // 5) Localiza o pedido (bloqueando a linha enquanto atualizamos).
    $st = $pdo->prepare('SELECT id, valor, status FROM pedidos WHERE asaas_payment_id = ? FOR UPDATE');
    $st->execute([$paymentId]);
    $pedido = $st->fetch();

    if (!$pedido) {
        error_log("[webhooks-asaas] Pagamento $paymentId sem pedido correspondente.");
        $pdo->commit();
        webhookResponder(200, 'order_not_found');
    }

    // 6) Regras por tipo de evento.
    if ($novoStatus === 'pago') {
        $valorPago = (float) ($pagamento['value'] ?? 0);

        if (abs($valorPago - (float) $pedido['valor']) > 0.01) {
            // Valor diferente do esperado: NÃO libera o acesso, marca para revisão.
            error_log("[webhooks-asaas] Valor divergente no pedido {$pedido['id']}: pago $valorPago, esperado {$pedido['valor']}");
            $pdo->prepare("UPDATE pedidos SET status = 'divergente' WHERE id = ?")->execute([$pedido['id']]);
        } elseif ($pedido['status'] !== 'pago') {
            $pdo->prepare("UPDATE pedidos SET status = 'pago', pago_em = NOW() WHERE id = ?")->execute([$pedido['id']]);

            // >>> AQUI você libera o acesso do usuário ao conteúdo/plano. <<<
            // Exemplo: UPDATE usuarios SET plano_ativo = 1 WHERE id = (usuario do pedido)
        }
    } elseif ($pedido['status'] !== 'pago' || in_array($novoStatus, ['reembolsado', 'cancelado'], true)) {
        // Um pedido já pago só muda se for reembolsado/cancelado (não vira "vencido").
        $pdo->prepare('UPDATE pedidos SET status = ? WHERE id = ?')->execute([$novoStatus, $pedido['id']]);

        // Se reembolsou, aqui você também deve REMOVER o acesso do usuário.
    }

    $pdo->commit();
    webhookResponder(200, 'ok');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // desfaz inclusive o registro do evento
    }
    error_log('[webhooks-asaas] Erro: ' . $e->getMessage());
    webhookResponder(500, 'error'); // 5xx faz o Asaas tentar de novo depois
}
