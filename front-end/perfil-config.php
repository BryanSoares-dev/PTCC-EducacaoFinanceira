<?php
session_start();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
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
    header("Location: ../front-end/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | FinControl</title>

    <link rel="stylesheet" href="../css/style-perfil.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    
    
</head>

<body>

<div class="background_shapes">
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>
</div>

<a href="../home.php" class="btn_voltar">
    ← Voltar
</a>

<main class="perfil_container">

    <section class="perfil_header">

        
    </section>

    <a href="">
    <section class="perfil_card">
        <div class="containerCard">
            <span class="material-icons">accessibility</span>
            <h2>Preferências</h2>
        </div>
        <p class="arrow"><span class="material-icons">arrow_forward_ios</span></p>
        <label>Personalize sua experiência: moeda, tema, idioma e outras preferências</label>
    </a>

    </section>

    <section class="perfil_card">

        

    </section>

</main>

</body>
</html>