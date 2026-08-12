<?php
session_start();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de ajuda | FinControl</title>

    <link rel="stylesheet" href="../css/paginas-suporte.css">

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
    Voltar
</a>

<main class="pagina_container">

    <!-- ===================== CABEÇALHO ===================== -->
    <section class="pagina_header">
        <div class="pagina_header_icon">
            <span class="material-icons">help_outline</span>
        </div>
        <div>
            <h1>Central de ajuda</h1>
            <p>Tire dúvidas frequentes ou fale com o nosso suporte</p>
        </div>
    </section>

    <!-- ===================== BUSCA ===================== -->
    <div class="pagina_search glass-card">
        <span class="material-icons">search</span>
        <input type="text" id="busca-ajuda" placeholder="Busque por um assunto (ex: transações, senha, exportar dados...)">
    </div>

    <!-- ===================== FAQ ===================== -->
    <h2 class="pagina_section_title">Perguntas frequentes</h2>

    <div class="faq_list" id="faq-list">

        <div class="faq_item glass-card">
            <button type="button" class="faq_question" data-faq-toggle>
                <span>Como eu adiciono uma nova transação?</span>
                <span class="material-icons faq_arrow">expand_more</span>
            </button>
            <div class="faq_answer">
                <p>Na tela inicial, toque no botão "+" para adicionar uma nova receita ou despesa. Preencha o valor, a categoria e a data, depois toque em salvar.</p>
            </div>
        </div>

        <div class="faq_item glass-card">
            <button type="button" class="faq_question" data-faq-toggle>
                <span>Como altero minha senha?</span>
                <span class="material-icons faq_arrow">expand_more</span>
            </button>
            <div class="faq_answer">
                <p>Vá em Configurações &gt; Senha e login, dentro da sua página de perfil. Lá você pode definir uma nova senha a qualquer momento.</p>
            </div>
        </div>

        <div class="faq_item glass-card">
            <button type="button" class="faq_question" data-faq-toggle>
                <span>Consigo exportar meus dados financeiros?</span>
                <span class="material-icons faq_arrow">expand_more</span>
            </button>
            <div class="faq_answer">
                <p>Sim! Em Configurações &gt; Privacidade e dados, você encontra a opção "Exportar meus dados", que gera um arquivo CSV com todo o seu histórico.</p>
            </div>
        </div>

        <div class="faq_item glass-card">
            <button type="button" class="faq_question" data-faq-toggle>
                <span>O FinControl é gratuito?</span>
                <span class="material-icons faq_arrow">expand_more</span>
            </button>
            <div class="faq_answer">
                <p>Sim, todas as funcionalidades principais do FinControl são gratuitas para uso pessoal.</p>
            </div>
        </div>

        <div class="faq_item glass-card">
            <button type="button" class="faq_question" data-faq-toggle>
                <span>Como excluo minha conta permanentemente?</span>
                <span class="material-icons faq_arrow">expand_more</span>
            </button>
            <div class="faq_answer">
                <p>Acesse Configurações &gt; Privacidade e dados e, na seção "Zona de risco", toque em "Excluir conta". Essa ação é irreversível.</p>
            </div>
        </div>

    </div>

    <!-- ===================== CONTATO ===================== -->
    <h2 class="pagina_section_title">Ainda precisa de ajuda?</h2>

    <div class="pagina_card glass-card">
        <div class="pagina_card_icon">
            <span class="material-icons">mail_outline</span>
        </div>
        <div class="pagina_card_text">
            <h3>Fale com o suporte</h3>
            <p>Nossa equipe responde em até 24h úteis pelo e-mail suporte@fincontrol.com</p>
        </div>
        <a href="mailto:suporte@fincontrol.com" class="btn_pagina_secundario">
            <span class="material-icons">send</span>
            Enviar e-mail
        </a>
    </div>

</main>

<script src="../js/faq.js"></script>

</body>
</html>