<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../front-end/login.php');
    exit;
}

require_csrf();

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$senha = (string) ($_POST['senha'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    header('Location: ../front-end/login.php?erro=credenciais_invalidas');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, nome, email, telefone, foto, senha, tipo, provedor FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || empty($usuario['senha']) || !password_verify($senha, $usuario['senha'])) {
        header('Location: ../front-end/login.php?erro=credenciais_invalidas');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['id'] = (int) $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['telefone'] = $usuario['telefone'];
    $_SESSION['foto'] = $usuario['foto'];
    $_SESSION['tipo'] = $usuario['tipo'];
    $_SESSION['usuario'] = [
        'id' => (int) $usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email'],
        'telefone' => $usuario['telefone'],
        'foto' => $usuario['foto'],
    ];

    header('Location: ../front-end/home.php');
    exit;
} catch (Throwable $e) {
    error_log('Erro no login: ' . $e->getMessage());
    header('Location: ../front-end/login.php?erro=indisponivel');
    exit;
}
