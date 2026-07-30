<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, nome, email, telefone, foto
    FROM usuarios
    WHERE id = ?
");

$stmt->execute([$_SESSION['id']]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | FinControl</title>

    <link rel="stylesheet" href="css/style-perfil.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    
</head>

<body>

<div class="background_shapes">
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>
</div>

<a href="home.php" class="btn_voltar">
    ← Voltar
</a>

<main class="perfil_container">

    <section class="perfil_header">
        
    </section>

        <a href="">
    <section class="perfil_card">
        <div class="containerCard">
            <span class="material-icons">account_circle</span>
            <h2>Minha conta</h2>
        </div>
        <p class="arrow"><span class="material-icons" style="color: #16E28A">arrow_forward_ios</span></p>
        <label>Atualize suas informações pessoais como: e-mail, nome de usuário e senha</label>
    </a>

    </section>

    <a href="">
    <section class="perfil_card">
        <div class="containerCard">
            <span class="material-icons">accessibility</span>
            <h2>Preferências</h2>
        </div>
        <p class="arrow"><span class="material-icons" style="color: #16E28A">arrow_forward_ios</span></p>
        <label>Personalize sua experiência: moeda, tema, idioma e outras preferências</label>
    </a>

    </section>

    <a href="">
    <section class="perfil_card">
        <div class="containerCard">
            <span class="material-icons">privacy_tip</span>
            <h2>Privacidades</h2>
        </div>
        <p class="arrow"><span class="material-icons" style="color: #16E28A">arrow_forward_ios</span></p>
        <label>Gerencie seus dados, privacidade e permissões da sua conta</label>
    </a>

    </section>

    <a href="">
    <section class="perfil_card">
        <div class="containerCard">
            <span class="material-icons">integration_instructions</span>
            <h2>Integrações</h2>
        </div>
        <p class="arrow"><span class="material-icons" style="color: #16E28A">arrow_forward_ios</span></p>
        <label>Conecte sua Agenda Financeira com outras plataformas e serviços</label>
    </a>

    </section>

</main>

</body>
</html>