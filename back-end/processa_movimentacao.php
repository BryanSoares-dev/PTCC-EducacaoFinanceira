<?php

session_start();
require_once("conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

$tipo = $_POST['tipo'];
$valor = $_POST['valor'];
$categoria = $_POST['categoria'];
$descricao = $_POST['descricao'];

$sql = "
INSERT INTO movimentacoes
(
    usuario_id,
    tipo,
    categoria,
    descricao,
    valor
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?
)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $usuario_id,
    $tipo,
    $categoria,
    $descricao,
    $valor
]);

header("Location: carteira.php");
exit();