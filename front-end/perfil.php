<?php
require_once __DIR__ . '/../back-end/bootstrap.php';
require_once __DIR__ . '/../back-end/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../front-end/login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nome, email, telefone, foto, banner, tema FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    session_destroy();
    header('Location: ../front-end/login.php');
    exit;
}

$tema = in_array($usuario['tema'] ?? '', ['claro', 'escuro', 'sistema'], true) ? $usuario['tema'] : 'sistema';
$avatar = !empty($usuario['foto']) ? $usuario['foto'] : '../img/default-avatar.svg';
$banner = !empty($usuario['banner']) ? $usuario['banner'] : '';
$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';
$mensagensErro = [
    'upload_falhou' => 'Não foi possível enviar o arquivo.',
    'arquivo_grande' => 'A foto deve ter no máximo 5 MB.',
    'banner_grande' => 'O banner deve ter no máximo 8 MB.',
    'formato_invalido' => 'Use uma imagem JPG, PNG, WEBP ou GIF válida.',
    'pasta_indisponivel' => 'A pasta de uploads não está disponível.',
    'indisponivel' => 'Não foi possível atualizar a imagem agora.',
];
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= htmlspecialchars($tema, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | AFDE</title>
    <link rel="stylesheet" href="../css/app.css">
    <link rel="icon" type="image/svg+xml" href="../img/favicon.svg">
</head>
<body>
<div class="background_shapes"><div class="shape shape1"></div><div class="shape shape2"></div><div class="shape shape3"></div></div>
<a href="#" onclick="history.back(); return false;" class="btn_voltar">← Voltar</a>

<main class="perfil_container perfil-page">
    <?php if (isset($mensagensErro[$erro])): ?><div class="perfil_alert perfil_alert-error"><?= htmlspecialchars($mensagensErro[$erro]) ?></div><?php endif; ?>
    <?php if (in_array($sucesso, ['foto', 'banner'], true)): ?><div class="perfil_alert perfil_alert-success"><?= $sucesso === 'banner' ? 'Banner atualizado com sucesso.' : 'Foto de perfil atualizada com sucesso.' ?></div><?php endif; ?>

    <section class="perfil_header perfil-header-cover <?= $banner ? 'has-banner' : '' ?>">
        <?php if ($banner): ?><img class="perfil-banner-image" src="<?= htmlspecialchars($banner, ENT_QUOTES, 'UTF-8') ?>" alt="Banner do perfil"><?php endif; ?>
        <div class="perfil-cover-overlay"></div>
        <form class="perfil-avatar-form" action="../back-end/atualizar_foto.php" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="tipo" value="foto">
            <div class="perfil_avatar">
                <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async" alt="Foto de perfil de <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="foto" class="btn_secondary">Alterar foto</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
            </div>
        </form>
        <div class="perfil_info perfil-info-cover">
            <h1><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <form class="perfil-banner-form" action="../back-end/atualizar_foto.php" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="tipo" value="banner">
            <label for="banner" class="btn_secondary">Alterar banner</label>
            <input type="file" id="banner" name="banner" accept="image/jpeg,image/png,image/webp,image/gif" hidden onchange="this.form.submit();">
            <small>JPG, PNG, WEBP ou GIF · até 8 MB</small>
        </form>
    </section>

    <section class="perfil_card">
        <h2>Informações Pessoais</h2>
        <form action="../back-end/atualizar_perfil.php" method="POST">
            <?= csrf_field() ?>
            <div class="form_group"><label>Nome Completo</label><input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form_group"><label>E-mail</label><input type="email" name="email" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form_group"><label>Telefone</label><input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <button type="submit" class="btn_primary">Salvar Alterações</button>
        </form>
    </section>

    <section class="perfil_card">
        <h2>Segurança</h2>
        <form action="../back-end/alterar_senha.php" method="POST">
            <?= csrf_field() ?>
            <div class="form_group"><label>Senha Atual</label><input type="password" name="senha_atual" minlength="6" maxlength="12" required></div>
            <div class="form_group"><label>Nova Senha</label><input type="password" name="nova_senha" minlength="6" maxlength="12" required></div>
            <div class="form_group"><label>Confirmar Nova Senha</label><input type="password" name="confirmar_senha" minlength="6" maxlength="12" required></div>
            <button type="submit" class="btn_primary">Alterar Senha</button>
        </form>
    </section>
</main>
<section class="crop-modal" id="cropModal" hidden aria-modal="true" role="dialog" aria-labelledby="cropTitle">
    <div class="crop-dialog">
        <button type="button" class="crop-close" data-crop-close aria-label="Fechar editor">×</button>
        <div class="crop-heading">
            <span class="crop-kicker">Personalização do perfil</span>
            <h2 id="cropTitle">Ajuste sua foto</h2>
            <p>Arraste a imagem, use o zoom e deixe o recorte exatamente como quiser.</p>
        </div>
        <div class="crop-workspace">
            <div class="crop-stage">
                <canvas id="cropCanvas" aria-label="Área de recorte quadrada"></canvas>
                <div class="crop-guide" aria-hidden="true"></div>
            </div>
            <div class="crop-preview-wrap">
                <span>Prévia 1:1</span>
                <img id="cropPreview" alt="Prévia da foto recortada">
            </div>
        </div>
        <label class="crop-zoom-label" for="cropZoom">Zoom</label>
        <input id="cropZoom" class="crop-zoom" type="range" min="1" max="3" step=".01" value="1">
        <div class="crop-actions">
            <button type="button" class="crop-button crop-button-ghost" data-crop-close>Cancelar</button>
            <button type="button" class="crop-button crop-button-primary" id="cropConfirm">Usar esta foto</button>
        </div>
    </div>
</section>
<script src="../JS/perfil-crop.js" defer></script>
</body>
</html>
