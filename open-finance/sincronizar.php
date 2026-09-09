<?php

session_start();
require_once '../back-end/conexao.php';
require_once 'pluggy-helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    pluggyJsonResponse(['success' => false, 'error' => 'Método não permitido.'], 405);
}

if (!isset($_SESSION['id'])) {
    pluggyJsonResponse(['success' => false, 'error' => 'Usuário não autenticado.'], 401);
}

try {
    $resultado = openFinanceSincronizarUsuario($pdo, (int) $_SESSION['id']);
    pluggyJsonResponse([
        'success' => true,
        'connected' => $resultado['connected'] ?? false,
        'sincronizado' => $resultado['sincronizado'] ?? 0,
        'avisos' => $resultado['avisos'] ?? [],
    ]);
} catch (Throwable $exception) {
    pluggyJsonResponse([
        'success' => false,
        'error' => 'Não foi possível sincronizar a conta.',
        'details' => $exception->getMessage(),
    ], 500);
}
