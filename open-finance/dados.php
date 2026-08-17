<?php
session_start();
require_once '../back-end/conexao.php';
require_once 'pluggy-helper.php';

if (!isset($_SESSION['id'])) {
    pluggyJsonResponse(['error' => 'Usuário não autenticado.'], 401);
}

$itemId = getItemIdDoUsuario($pdo, (int) $_SESSION['id']);
if (!$itemId) {
    pluggyJsonResponse(['connected' => false, 'transacoes' => [], 'emprestimos' => [], 'investimentos' => []]);
}

$apiKey = pluggyAuth();
if (!$apiKey) {
    pluggyJsonResponse(['error' => 'A integração Pluggy não está configurada. Defina PLUGGY_CLIENT_ID e PLUGGY_CLIENT_SECRET no ambiente do PHP.'], 503);
}

pluggyJsonResponse(['connected' => true] + pluggyDadosDoItem($itemId, $apiKey));
