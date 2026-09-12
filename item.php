<?php

declare(strict_types=1);
require_once __DIR__ . '/back-end/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/back-end/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$itemId = is_array($body) ? trim((string) ($body['itemId'] ?? '')) : '';

if ($itemId === '' || mb_strlen($itemId) > 255) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Identificador do item inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE usuarios SET itemid = :itemid WHERE id = :id');
    $stmt->execute([':itemid' => $itemId, ':id' => (int) $_SESSION['id']]);
    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Erro ao salvar item: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Não foi possível salvar o item.']);
}
