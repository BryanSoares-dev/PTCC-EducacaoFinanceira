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
</head>

<body>

<div class="background_shapes">
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>
</div>

<a href="../front-end/home.php" class="btn_voltar">
    ← Voltar
</a>

<main class="perfil_container">

    <section class="perfil_header">

        <form
            action="atualizar_foto.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="perfil_avatar">

                <img
                src="<?= !empty($usuario['foto'])
                    ? htmlspecialchars($usuario['foto'])
                    : '../img/default.png'; ?>"
                alt="Avatar"
            >

                <label for="foto" class="btn_secondary">
                    Alterar Foto
                </label>

                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept="image/*"
                    hidden
                    onchange="this.form.submit();"
                >

            </div>

        </form>

        <div class="perfil_info">
            <h1><?= htmlspecialchars($usuario['nome']) ?></h1>
            <p><?= htmlspecialchars($usuario['email']) ?></p>
        </div>

    </section>

    <section class="perfil_card">

        <h2>Informações Pessoais</h2>

        <form action="atualizar_perfil.php" method="POST">

            <div class="form_group">
                <label>Nome Completo</label>

                <input
                    type="text"
                    name="nome"
                    value="<?= htmlspecialchars($usuario['nome']) ?>"
                    required
                >
            </div>

            <div class="form_group">
                <label>E-mail</label>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($usuario['email']) ?>"
                    required
                >
            </div>

            <div class="form_group">
                <label>Telefone</label>

                <input
                    type="text"
                    name="telefone"
                    value="<?= htmlspecialchars($usuario['telefone']) ?>"
                >
            </div>

            <button type="submit" class="btn_primary">
                Salvar Alterações
            </button>

        </form>

    </section>

    <section class="perfil_card">

        <h2>Segurança</h2>

        <form action="alterar_senha.php" method="POST">

            <div class="form_group">
                <label>Senha Atual</label>

                <input
                    type="password"
                    name="senha_atual"
                    minlength="6"
                    maxlength="12"
                    required
                >
            </div>

            <div class="form_group">
                <label>Nova Senha</label>

                <input
                    type="password"
                    name="nova_senha"
                    minlength="6"
                    maxlength="12"
                    required
                >
            </div>

            <div class="form_group">
                <label>Confirmar Nova Senha</label>

                <input
                    type="password"
                    name="confirmar_senha"
                    minlength="6"
                    maxlength="12"
                    required
                >
            </div>

            <button type="submit" class="btn_primary">
                Alterar Senha
            </button>

        </form>

    </section>

</main>

</body>
</html>