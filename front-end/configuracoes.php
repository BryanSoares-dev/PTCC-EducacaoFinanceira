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

// Iniciais para o avatar-placeholder, caso o usuário não tenha foto
$partesNome = explode(" ", trim($usuario['nome']));
$iniciais = strtoupper(substr($partesNome[0], 0, 1));
if (count($partesNome) > 1) {
    $iniciais .= strtoupper(substr(end($partesNome), 0, 1));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações | FinControl</title>

    <link rel="stylesheet" href="../css/configuracoes.css">

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
    <span class="material-icons">arrow_back</span>
    Voltar
</a>

<main class="perfil_container">

    <!-- ===================== CABEÇALHO DO PERFIL ===================== -->
    <section class="perfil_header glass">
        <div class="perfil_header_info">

            <?php if (!empty($usuario['foto'])): ?>
                <img src="../uploads/<?= htmlspecialchars($usuario['foto']) ?>" alt="Foto de perfil" class="perfil_avatar">
            <?php else: ?>
                <div class="perfil_avatar perfil_avatar_placeholder"><?= htmlspecialchars($iniciais) ?></div>
            <?php endif; ?>

            <div class="perfil_dados">
                <h1><?= htmlspecialchars($usuario['nome']) ?></h1>
                <p class="perfil_email"><span class="material-icons">mail_outline</span> <?= htmlspecialchars($usuario['email']) ?></p>
                <?php if (!empty($usuario['telefone'])): ?>
                    <p class="perfil_telefone"><span class="material-icons">call</span> <?= htmlspecialchars($usuario['telefone']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <a href="perfil.php" class="btn_editar_perfil">
            <span class="material-icons">edit</span>
            Editar perfil
        </a>
    </section>

    <!-- ===================== SEÇÃO: CONTA ===================== -->
    <h2 class="settings_section_title">Conta</h2>
    <div class="settings_grid">

        <a href="perfil.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">person</span>
            </div>
            <div class="settings_card_text">
                <h3>Dados pessoais</h3>
                <p>Nome, e-mail, telefone e foto de perfil</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

        <a href="preferencias.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">tune</span>
            </div>
            <div class="settings_card_text">
                <h3>Preferências</h3>
                <p>Personalize sua experiência: moeda, tema, idioma e outras preferências</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

        <a href="notificacoes.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">notifications_none</span>
            </div>
            <div class="settings_card_text">
                <h3>Notificações</h3>
                <p>Escolha como e quando você quer ser avisado</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

    </div>

    <!-- ===================== SEÇÃO: SEGURANÇA E PRIVACIDADE ===================== -->
    <h2 class="settings_section_title">Segurança e privacidade</h2>
    <div class="settings_grid">

        <a href="alterar-senha.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">lock_outline</span>
            </div>
            <div class="settings_card_text">
                <h3>Senha e login</h3>
                <p>Altere sua senha e gerencie o acesso à sua conta</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

        <a href="privacidade.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">shield</span>
            </div>
            <div class="settings_card_text">
                <h3>Privacidade e dados</h3>
                <p>Controle o uso dos seus dados e exporte suas informações</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

    </div>

    <!-- ===================== SEÇÃO: SUPORTE ===================== -->
    <h2 class="settings_section_title">Suporte</h2>
    <div class="settings_grid">

        <a href="ajuda.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">help_outline</span>
            </div>
            <div class="settings_card_text">
                <h3>Central de ajuda</h3>
                <p>Tire dúvidas e entre em contato com o suporte</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

        <a href="sobre.php" class="settings_card glass-card">
            <div class="settings_card_icon">
                <span class="material-icons">info_outline</span>
            </div>
            <div class="settings_card_text">
                <h3>Sobre o FinControl</h3>
                <p>Versão do aplicativo, termos de uso e política de privacidade</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>

    </div>

    <!-- ===================== SAIR ===================== -->
    <div class="settings_grid">
        <a href="../back-end/logout.php" class="settings_card glass-card settings_card_danger">
            <div class="settings_card_icon">
                <span class="material-icons">logout</span>
            </div>
            <div class="settings_card_text">
                <h3>Sair da conta</h3>
                <p>Encerrar sua sessão neste dispositivo</p>
            </div>
            <span class="material-icons settings_card_arrow">arrow_forward_ios</span>
        </a>
    </div>

</main>

</body>
</html>