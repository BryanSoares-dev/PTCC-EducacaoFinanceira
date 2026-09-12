<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../front-end/cadastro.php');
    exit;
}

require_csrf();

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$senha = (string) ($_POST['senha'] ?? '');
$confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');
$telefone = trim((string) ($_POST['telefone'] ?? ''));

if ($nome === '' || mb_strlen($nome) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../front-end/cadastro.php?erro=dados_invalidos');
    exit;
}
if (strlen($senha) < 8 || $senha !== $confirmarSenha) {
    header('Location: ../front-end/cadastro.php?erro=senha_invalida');
    exit;
}
if (mb_strlen($telefone) > 25) {
    header('Location: ../front-end/cadastro.php?erro=telefone_invalido');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, senha, provedor FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        if ($usuario['provedor'] === 'google' && empty($usuario['senha'])) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE usuarios SET senha = ?, provedor = 'ambos' WHERE id = ?");
            $update->execute([$senhaHash, (int) $usuario['id']]);
            header('Location: ../front-end/login.php?sucesso=senha_definida');
            exit;
        }
        header('Location: ../front-end/cadastro.php?erro=email_existente');
        exit;
    }

    $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, telefone, provedor) VALUES (?, ?, ?, ?, 'local')");
    $insert->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT), $telefone]);
    header('Location: ../front-end/login.php?sucesso=cadastro');
    exit;
} catch (Throwable $e) {
    error_log('Erro no cadastro: ' . $e->getMessage());
    header('Location: ../front-end/cadastro.php?erro=indisponivel');
    exit;
}
