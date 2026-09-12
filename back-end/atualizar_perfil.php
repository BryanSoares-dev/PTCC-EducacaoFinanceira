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

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$telefone = trim((string) ($_POST['telefone'] ?? ''));

if ($nome === '' || mb_strlen($nome) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255 || mb_strlen($telefone) > 25) {
    header('Location: ../front-end/perfil.php?erro=dados_invalidos');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1');
    $stmt->execute([$email, (int) $_SESSION['id']]);
    if ($stmt->fetchColumn()) {
        header('Location: ../front-end/perfil.php?erro=email_existente');
        exit;
    }

    $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?');
    $stmt->execute([$nome, $email, $telefone, (int) $_SESSION['id']]);

    $_SESSION['nome'] = $nome;
    $_SESSION['email'] = $email;
    $_SESSION['telefone'] = $telefone;
    header('Location: ../front-end/perfil.php?sucesso=dados');
    exit;
} catch (Throwable $e) {
    error_log('Erro ao atualizar perfil: ' . $e->getMessage());
    header('Location: ../front-end/perfil.php?erro=indisponivel');
    exit;
}
