<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/conexao.php';

if (empty($_SESSION['id'])) {
    header('Location: ../front-end/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../front-end/carteira.php');
    exit;
}

require_csrf();

$tipo = (string) ($_POST['tipo'] ?? '');
$valorBruto = str_replace(',', '.', trim((string) ($_POST['valor'] ?? '')));
$valor = filter_var($valorBruto, FILTER_VALIDATE_FLOAT);
$categoria = trim((string) ($_POST['categoria'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));

if (!in_array($tipo, ['entrada', 'saida'], true) || $valor === false || $valor <= 0 || $valor > 99999999.99 || $categoria === '' || mb_strlen($categoria) > 100 || mb_strlen($descricao) > 255) {
    header('Location: ../front-end/carteira.php?erro=movimentacao_invalida');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO movimentacoes (usuario_id, tipo, categoria, descricao, valor) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([(int) $_SESSION['id'], $tipo, $categoria, $descricao, number_format((float) $valor, 2, '.', '')]);

header('Location: ../front-end/carteira.php?sucesso=movimentacao');
exit;
