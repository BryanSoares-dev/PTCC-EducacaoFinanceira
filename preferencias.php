<?php
session_start();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, nome, email, telefone, foto, moeda, tema, idioma, resumo_semanal, alertas_metas
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

// ===================== MENSAGEM VINDA DO REDIRECT =====================
$status = $_GET['status'] ?? null;

$mensagem = $status === 'sucesso' ? "Preferências salvas com sucesso." : null;
$erro     = $status === 'erro' ? "Não foi possível salvar suas preferências. Tente novamente." : null;

$moedaAtual  = $usuario['moeda']  ?? 'BRL';
$temaAtual   = $usuario['tema']   ?? 'sistema';
$idiomaAtual = $usuario['idioma'] ?? 'pt-BR';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferências | FinControl</title>

    <link rel="stylesheet" href="../css/style-perfil.css">
    <link rel="stylesheet" href="../css/preferencias.css">

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

<a href="configuracoes.php" class="btn_voltar">
    <span class="material-icons">arrow_back</span>
    Configurações
</a>

<main class="perfil_container">

    <div class="prefs_header">
        <h1>Preferências</h1>
        <p>Personalize sua experiência: moeda, tema, idioma e outras preferências.</p>
    </div>

    <?php if ($mensagem): ?>
        <div class="prefs_alert prefs_alert_success">
            <span class="material-icons">check_circle</span>
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="prefs_alert prefs_alert_error">
            <span class="material-icons">error_outline</span>
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="salvar_preferencias.php" class="prefs_form">

        <!-- ===================== APARÊNCIA ===================== -->
        <section class="prefs_card glass-card">
            <div class="prefs_card_head">
                <span class="material-icons">palette</span>
                <div>
                    <h2>Aparência</h2>
                    <p>Escolha como o FinControl deve aparecer para você</p>
                </div>
            </div>

            <div class="segmented_control" role="radiogroup" aria-label="Tema">

                <input type="radio" id="tema_claro" name="tema" value="claro" <?= $temaAtual === 'claro' ? 'checked' : '' ?>>
                <label for="tema_claro">
                    <span class="material-icons">light_mode</span>
                    Claro
                </label>

                <input type="radio" id="tema_escuro" name="tema" value="escuro" <?= $temaAtual === 'escuro' ? 'checked' : '' ?>>
                <label for="tema_escuro">
                    <span class="material-icons">dark_mode</span>
                    Escuro
                </label>

                <input type="radio" id="tema_sistema" name="tema" value="sistema" <?= $temaAtual === 'sistema' ? 'checked' : '' ?>>
                <label for="tema_sistema">
                    <span class="material-icons">settings_suggest</span>
                    Sistema
                </label>

            </div>
        </section>

        <!-- ===================== MOEDA E IDIOMA ===================== -->
        <section class="prefs_card glass-card">
            <div class="prefs_card_head">
                <span class="material-icons">public</span>
                <div>
                    <h2>Região</h2>
                    <p>Moeda e idioma usados em toda a plataforma</p>
                </div>
            </div>

            <div class="prefs_field">
                <label for="moeda">Moeda</label>
                <div class="prefs_select_wrapper">
                    <select id="moeda" name="moeda">
                        <option value="BRL" <?= $moedaAtual === 'BRL' ? 'selected' : '' ?>>Real brasileiro (R$)</option>
                        <option value="USD" <?= $moedaAtual === 'USD' ? 'selected' : '' ?>>Dólar americano ($)</option>
                        <option value="EUR" <?= $moedaAtual === 'EUR' ? 'selected' : '' ?>>Euro (€)</option>
                    </select>
                    <span class="material-icons prefs_select_icon">expand_more</span>
                </div>
            </div>

            <div class="prefs_field">
                <label for="idioma">Idioma</label>
                <div class="prefs_select_wrapper">
                    <select id="idioma" name="idioma">
                        <option value="pt-BR" <?= $idiomaAtual === 'pt-BR' ? 'selected' : '' ?>>Português (Brasil)</option>
                        <option value="en-US" <?= $idiomaAtual === 'en-US' ? 'selected' : '' ?>>English (US)</option>
                        <option value="es-ES" <?= $idiomaAtual === 'es-ES' ? 'selected' : '' ?>>Español</option>
                    </select>
                    <span class="material-icons prefs_select_icon">expand_more</span>
                </div>
            </div>
        </section>

        <!-- ===================== NOTIFICAÇÕES RÁPIDAS ===================== -->
        <section class="prefs_card glass-card">
            <div class="prefs_card_head">
                <span class="material-icons">notifications_none</span>
                <div>
                    <h2>Notificações</h2>
                    <p>Alertas rápidos direto na plataforma</p>
                </div>
            </div>

            <div class="prefs_toggle_row">
                <div>
                    <h3>Resumo semanal</h3>
                    <p>Receba um resumo dos seus gastos toda semana</p>
                </div>
                <label class="switch">
                    <input type="checkbox" name="resumo_semanal" <?= !empty($usuario['resumo_semanal']) ? 'checked' : '' ?>>
                    <span class="switch_slider"></span>
                </label>
            </div>

            <div class="prefs_toggle_row">
                <div>
                    <h3>Alertas de metas</h3>
                    <p>Avise quando eu estiver perto do limite de uma meta</p>
                </div>
                <label class="switch">
                    <input type="checkbox" name="alertas_metas" <?= !empty($usuario['alertas_metas']) ? 'checked' : '' ?>>
                    <span class="switch_slider"></span>
                </label>
            </div>
        </section>

        <div class="prefs_actions">
            <button type="submit" class="btn_salvar_prefs">
                <span class="material-icons">save</span>
                Salvar alterações
            </button>
        </div>

    </form>

</main>

</body>
</html>