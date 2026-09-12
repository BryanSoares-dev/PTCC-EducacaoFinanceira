<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/conexao.php';

if (empty($_SESSION['id'])) {
    header('Location: ../front-end/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../front-end/perfil.php');
    exit;
}
require_csrf();

$senhaAtual = (string) ($_POST['senha_atual'] ?? '');
$novaSenha = (string) ($_POST['nova_senha'] ?? '');
$confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

if ($senhaAtual === '' || strlen($novaSenha) < 8 || strlen($novaSenha) > 72 || !hash_equals($novaSenha, $confirmarSenha)) {
    header('Location: ../front-end/perfil.php?erro=senha_invalida');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT senha FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $_SESSION['id']]);
    $senhaArmazenada = (string) $stmt->fetchColumn();

    if ($senhaArmazenada === '' || !password_verify($senhaAtual, $senhaArmazenada)) {
        header('Location: ../front-end/perfil.php?erro=senha_atual');
        exit;
    }

    $update = $pdo->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
    $update->execute([password_hash($novaSenha, PASSWORD_DEFAULT), (int) $_SESSION['id']]);
    session_regenerate_id(true);
    header('Location: ../front-end/perfil.php?sucesso=senha');
    exit;
} catch (Throwable $e) {
    error_log('Erro ao alterar senha: ' . $e->getMessage());
    header('Location: ../front-end/perfil.php?erro=indisponivel');
    exit;
}
