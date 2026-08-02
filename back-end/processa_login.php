<?php
session_start();
require_once 'conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se existe usuário E se ele tem senha definida antes de validar
    if ($usuario && !empty($usuario['senha']) && password_verify($senha, $usuario['senha'])) {

        $_SESSION['id'] = $usuario['id'];

        $_SESSION['usuario'] = [
            'id'       => $usuario['id'],
            'nome'     => $usuario['nome'],
            'email'    => $usuario['email'],
            'telefone' => $usuario['telefone'],
            'foto'     => !empty($usuario['foto']) ? $usuario['foto'] : null,
        ];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['telefone'] = $usuario['telefone'];
        $_SESSION['foto'] = $usuario['foto'];

        header("Location: ../front-end/home.php");
        exit;

    } elseif ($usuario && empty($usuario['senha'])) {
        // Conta existe, mas foi criada só via Google — não tem senha ainda
        echo "<script>alert('Esta conta foi criada com Google. Faça login pelo Google ou defina uma senha na tela de cadastro.'); window.history.back();</script>";
        exit;

    } else {
        echo "<script>alert('E-mail ou senha incorretos!'); window.history.back();</script>";
        exit;
    }

} catch (PDOException $e) {
    die("Erro no login: " . $e->getMessage());
}
?>