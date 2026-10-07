<?php

require_once __DIR__ . '/back-end/seguranca.php';
require_once __DIR__ . '/back-end/conexao.php';

header('Content-Type: application/json; charset=utf-8');

/* ------------------------------------------------------------------
 * 1) Autenticação do webhook
 * O Asaas envia o token configurado no painel no header
 * "asaas-access-token". Sem token configurado no .env, recusa tudo.
 * ---------------------------------------------------------------- */
$tokenEsperado = afdeConfig('ASAAS_WEBHOOK_TOKEN', '');
$tokenRecebido = $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ?? '';

if ($tokenEsperado === '' || !hash_equals($tokenEsperado, $tokenRecebido)) {
    error_log('[webhooks-asaas] Requisição recusada: token de webhook ausente/inválido.');
    http_response_code(401);
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

/* ------------------------------------------------------------------
 * 2) Leitura do evento
 * ---------------------------------------------------------------- */
$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(['status' => 'invalid_payload']);
    exit;
}

$evento    = (string) ($dados['event'] ?? '');
$pagamento = is_array($dados['payment'] ?? null) ? $dados['payment'] : [];
$idPagamento = (string) ($pagamento['id'] ?? '');

if ($evento === '' || $idPagamento === '') {
    // Evento sem pagamento (ex.: outros tipos). Responde 200 para não travar a fila.
    http_response_code(200);
    echo json_encode(['status' => 'ignored']);
    exit;
}

/* ------------------------------------------------------------------
 * 3) Mapeia o evento para o status do pedido
 * ---------------------------------------------------------------- */
$statusPorEvento = [
    'PAYMENT_RECEIVED'  => 'pago',       // Pix e boleto recebidos
    'PAYMENT_CONFIRMED' => 'pago',       // cartão confirmado
    'PAYMENT_OVERDUE'   => 'vencido',
    'PAYMENT_DELETED'   => 'cancelado',
    'PAYMENT_REFUNDED'  => 'reembolsado',
];

if (!isset($statusPorEvento[$evento])) {
    http_response_code(200);
    echo json_encode(['status' => 'ignored']);
    exit;
}

$novoStatus = $statusPorEvento[$evento];

/* ------------------------------------------------------------------
 * 4) Atualiza o pedido no banco
 * ---------------------------------------------------------------- */
try {
    $pdo = afdeDb();
    $pdo->beginTransaction();

    // Trava a linha do pedido para evitar processamento duplicado
    $st = $pdo->prepare(
        'SELECT id, usuario_id, plano, valor, status
           FROM pedidos
          WHERE asaas_payment_id = ?
          FOR UPDATE'
    );
    $st->execute([$idPagamento]);
    $pedido = $st->fetch();

    if (!$pedido) {
        $pdo->rollBack();
        error_log("[webhooks-asaas] Pedido não encontrado para o pagamento $idPagamento");
        http_response_code(200); // 200 para o Asaas não ficar reenviando
        echo json_encode(['status' => 'order_not_found']);
        exit;
    }

    if ($novoStatus === 'pago') {
        // Idempotência: o Asaas pode reenviar o mesmo evento
        if ($pedido['status'] === 'pago') {
            $pdo->commit();
            http_response_code(200);
            echo json_encode(['status' => 'already_processed']);
            exit;
        }

        // Confere o valor pago contra o valor do pedido
        $valorPago = (float) ($pagamento['value'] ?? 0);
        if (abs($valorPago - (float) $pedido['valor']) > 0.01) {
            $pdo->rollBack();
            error_log("[webhooks-asaas] Valor divergente no pedido {$pedido['id']}: esperado {$pedido['valor']}, recebido $valorPago");
            http_response_code(200);
            echo json_encode(['status' => 'amount_mismatch']);
            exit;
        }

        $up = $pdo->prepare("UPDATE pedidos SET status = 'pago', pago_em = NOW() WHERE id = ?");
        $up->execute([$pedido['id']]);

        // ----------------------------------------------------------
        // AQUI: libere o plano do usuário.
        // Exemplo (ajuste ao seu banco):
        //   $dias = $pedido['plano'] === 'anual' ? 365 : 30;
        //   UPDATE usuarios
        //      SET plano_expira_em = DATE_ADD(GREATEST(NOW(), COALESCE(plano_expira_em, NOW())), INTERVAL $dias DAY)
        //    WHERE id = {$pedido['usuario_id']}
        // ----------------------------------------------------------
    } else {
        // Nunca rebaixa um pedido já pago, exceto em reembolso
        if ($pedido['status'] === 'pago' && $novoStatus !== 'reembolsado') {
            $pdo->commit();
            http_response_code(200);
            echo json_encode(['status' => 'ignored']);
            exit;
        }

        $up = $pdo->prepare('UPDATE pedidos SET status = ? WHERE id = ?');
        $up->execute([$novoStatus, $pedido['id']]);

        // Em caso de reembolso, aqui você pode remover o acesso ao plano.
    }

    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[webhooks-asaas] Erro ao processar: ' . $e->getMessage());
    http_response_code(500); // 500 faz o Asaas tentar de novo mais tarde
    echo json_encode(['status' => 'error']);
    exit;
}

http_response_code(200);
echo json_encode(['status' => 'ok']);