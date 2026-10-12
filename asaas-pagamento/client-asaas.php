<?php
require_once __DIR__ . '/asaas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(405, ['erro' => 'Método não permitido.']);
}
$usuarioId = usuarioLogadoId();
csrf_exigir_header();
$entrada = lerEntrada();
$cpfCnpj = preg_replace('/\D+/', '', (string) ($entrada['cpfCnpj'] ?? ''));
if (!in_array(strlen($cpfCnpj), [11, 14], true)) {
    responderJson(422, ['erro' => 'Informe um CPF ou CNPJ válido.']);
}

try {
    $st = afdeDb()->prepare('SELECT nome, email, telefone FROM usuarios WHERE id = ? LIMIT 1');
    $st->execute([$usuarioId]);
    $usuario = $st->fetch();
    if (!$usuario) {
        responderJson(401, ['erro' => 'Não foi possível identificar sua conta.']);
    }
    $customerId = garantirClienteAsaas(
        $usuarioId,
        (string) $usuario['nome'],
        (string) $usuario['email'],
        $cpfCnpj,
        $usuario['telefone'] !== null ? (string) $usuario['telefone'] : null
    );
    responderJson(200, ['ok' => true, 'customerId' => $customerId]);
} catch (Throwable $e) {
    error_log('[client-asaas] ' . $e->getMessage());
    responderJson(502, ['erro' => 'Não foi possível preparar o cadastro de pagamento. Tente novamente.']);
}