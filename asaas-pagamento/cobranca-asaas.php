<?php
require_once __DIR__ . '/asaas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(405, ['erro' => 'Método não permitido.']);
}
$usuarioId = usuarioLogadoId();
csrf_exigir_header();
$entrada = lerEntrada();
$planoId = (string) ($entrada['plano'] ?? '');
$ciclo = (string) ($entrada['ciclo'] ?? '');
$cpfCnpj = preg_replace('/\D+/', '', (string) ($entrada['cpfCnpj'] ?? ''));
if (!in_array(strlen($cpfCnpj), [11, 14], true)) {
    responderJson(422, ['erro' => 'Informe um CPF ou CNPJ válido.']);
}
$planos = afdePlanos();
if (!isset($planos[$planoId][$ciclo])) {
    responderJson(422, ['erro' => 'Plano ou ciclo inválido.']);
}

try {
    $db = afdeDb();
    $st = $db->prepare('SELECT nome, email, telefone FROM usuarios WHERE id = ? LIMIT 1');
    $st->execute([$usuarioId]);
    $usuario = $st->fetch();
    if (!$usuario) {
        responderJson(401, ['erro' => 'Não foi possível identificar sua conta. Entre novamente.']);
    }

    $customerId = garantirClienteAsaas(
        $usuarioId,
        (string) $usuario['nome'],
        (string) $usuario['email'],
        $cpfCnpj,
        $usuario['telefone'] !== null ? (string) $usuario['telefone'] : null
    );
    $plano = $planos[$planoId][$ciclo];
    $st = $db->prepare("INSERT INTO pedidos (usuario_id, plano, valor, status) VALUES (?, ?, ?, 'pendente')");
    $st->execute([$usuarioId, $planoId . '_' . $ciclo, $plano['valor']]);
    $pedidoId = (int) $db->lastInsertId();

    try {
        $pagamento = criarCobrancaAsaas(
            $customerId,
            (float) $plano['valor'],
            date('Y-m-d', strtotime('+3 days')),
            $plano['nome'],
            (string) $pedidoId
        );
        if (empty($pagamento['id'])) {
            throw new RuntimeException('O Asaas não retornou o identificador da cobrança.');
        }
        $db->prepare('UPDATE pedidos SET asaas_payment_id = ?, invoice_url = ? WHERE id = ?')
            ->execute([$pagamento['id'], $pagamento['invoiceUrl'] ?? null, $pedidoId]);
    } catch (Throwable $e) {
        error_log('[cobranca-asaas] ' . $e->getMessage());
        $db->prepare("UPDATE pedidos SET status = 'erro' WHERE id = ?")->execute([$pedidoId]);
        responderJson(502, ['erro' => 'Não foi possível gerar a cobrança. Confira sua configuração e tente novamente.']);
    }

    try {
        $qr = buscarQrCodePix((string) $pagamento['id']);
    } catch (Throwable $e) {
        error_log('[cobranca-asaas] QR Code indisponível: ' . $e->getMessage());
        $qr = [];
    }
    responderJson(200, [
        'ok' => true,
        'pedidoId' => $pedidoId,
        'valor' => $plano['valor'],
        'invoiceUrl' => $pagamento['invoiceUrl'] ?? null,
        'pixCopiaECola' => $qr['payload'] ?? null,
        'pixImagem' => isset($qr['encodedImage']) ? 'data:image/png;base64,' . $qr['encodedImage'] : null,
        'pixExpiraEm' => $qr['expirationDate'] ?? null,
    ]);
} catch (Throwable $e) {
    error_log('[cobranca-asaas] ' . $e->getMessage());
    responderJson(502, ['erro' => 'Não foi possível iniciar o pagamento. Tente novamente mais tarde.']);
}