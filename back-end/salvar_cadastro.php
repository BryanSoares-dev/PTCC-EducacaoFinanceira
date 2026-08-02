<?php
session_start();
require_once 'conexao.php';

if ($_POST) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $telefone = $_POST['telefone'];

    if ($senha !== $confirmar_senha) {
        echo "<script>alert('Erro na confirmação de senha'); window.history.back();</script>";
        exit;
    }

    // Verifica se o e-mail já existe (com prepared statement, seguro contra SQL Injection)
    $sql = "SELECT id, senha,  FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Conta existe. Verifica se veio do Google e ainda não tem senha definida.
        if ($usuario[''] === 'google' && empty($usuario['senha'])) {

            // Em vez de bloquear, aproveita e define a senha nessa conta já existente
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sqlUpdate = "UPDATE usuarios SET senha = ?,  = 'ambos' WHERE email = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);

            if ($stmtUpdate->execute([$senhaHash, $email])) {
                $_SESSION['mensagem'] = "Senha adicionada à sua conta com sucesso! Agora você pode entrar com e-mail e senha.";
                header("Location: ../front-end/login.php");
                exit;
            } else {
                echo "<script>alert('Erro ao definir senha!'); window.history.back();</script>";
                exit;
            }

        } else {
            // Já existe conta local de verdade (com senha própria) → erro real
            echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
            exit;
        }
    }

    // E-mail não existe ainda → cria conta nova normalmente
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sqlInsert = "INSERT INTO usuarios (nome, email, senha, telefone, ) 
                  VALUES (?, ?, ?, ?, 'local')";
    $stmtInsert = $pdo->prepare($sqlInsert);

    if ($stmtInsert->execute([$nome, $email, $senhaHash, $telefone])) {
        $_SESSION['mensagem'] = "Cadastro realizado com sucesso!";
        header("Location: ../front-end/login.php");
        exit;
    } else {
        echo "<script>alert('Erro ao cadastrar!'); window.history.back();</script>";
        exit;
    }
}
?>