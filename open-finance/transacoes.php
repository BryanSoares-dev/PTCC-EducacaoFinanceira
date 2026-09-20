<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../back-end/conexao.php';
require_once 'pluggy-helper.php';

if (!isset($_SESSION['id'])) {
    pluggyJsonResponse(['error' => 'Usuário não autenticado.'], 401);
}

try {
    $usuarioId = (int) $_SESSION['id'];
    openFinanceEnsureSchema($pdo);
    $itemId = getItemIdDoUsuario($pdo, $usuarioId);

    if (!$itemId) {
        pluggyJsonResponse([
            'connected' => false,
            'error' => 'Nenhuma conta bancária conectada.',
            'transacoes' => [],
        ]);
    }

    $apiKey = pluggyAuth();
    if (!$apiKey) {
        pluggyJsonResponse([
            'connected' => true,
            'error' => 'Falha ao autenticar na Pluggy.',
            'transacoes' => [],
        ], 503);
    }

    $dados = pluggyDadosDoItem($itemId, $apiKey);
    $sincronizado = openFinancePersistirTransacoes($pdo, $usuarioId, $dados['transacoes'] ?? []);

    pluggyJsonResponse([
        'connected' => true,
        'transacoes' => $dados['transacoes'] ?? [],
        'sincronizado' => $sincronizado,
        'avisos' => $dados['avisos'] ?? [],
    ]);
} catch (Throwable $exception) {
    pluggyJsonResponse([
        'error' => 'Não foi possível consultar as transações.',
        'details' => $exception->getMessage(),
    ], 500);
}
