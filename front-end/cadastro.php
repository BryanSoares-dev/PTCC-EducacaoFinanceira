<?php
require_once __DIR__ . '/../back-end/seguranca.php';
iniciar_sessao_segura();
?>
<!DOCTYPE html>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | AFDE</title>

<link rel="stylesheet" href="../css/cadastro.css">
<link rel="stylesheet" href="../css/termos-modal.css">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="../img/favicon.png">

    <link rel="stylesheet" href="../css/liquid-glass.css?v=101">

</head>

<body>

<div class="background_shapes">
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
</div>

<a href="../front-end/home.php" class="btn_voltar">
    ← Voltar
</a>

<main class="cadastro_container">

    <section class="cadastro_left">

        <img src="../img/logo.png" alt="AFDE" class="brand_logo">

        <span class="badge">
            Plataforma de Educação Financeira
        </span>

        <h1>
            Comece sua jornada financeira hoje.
        </h1>

        <p>
            Crie sua conta gratuitamente e tenha acesso aos conteúdos,
            ferramentas e recursos que vão ajudar você a organizar seu
            dinheiro e planejar seu futuro.
        </p>

        <div class="benefits">

            <div class="benefit">
                <span class="benefit_icon">✓</span>
                <p>Cadastro rápido e gratuito</p>
            </div>

            <div class="benefit">
                <span class="benefit_icon">✓</span>
                <p>Calculadora financeira exclusiva</p>
            </div>

            <div class="benefit">
                <span class="benefit_icon">✓</span>
                <p>Conteúdo para iniciantes</p>
            </div>

        </div>

    </section>

    <section class="cadastro_right">

        <div class="cadastro_card">

            <div class="card_header">

                <h2>Criar Conta</h2>

                <p>
                    Preencha os dados abaixo para começar.
                </p>

            </div>

            <form class="cadastro_form" action="../back-end/salvar_cadastro.php" method="POST">

                <?= csrf_campo() ?>

                <div class="input_group">
                    <label>Nome Completo</label>

                    <input
                        type="text"
                        name="nome"
                        placeholder="Digite seu nome"
                        required
                    >
                </div>

                <div class="input_group">
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Digite seu email"
                        required
                    >
                </div>

                <div class="input_group">
                    <label>Senha</label>

                    <input
                        type="password"
                        name="senha"
                        placeholder="Crie uma senha"
                        required
                        minlength="6"
                        maxlength="12"
                    >
                </div>

                <div class="input_group">
                    <label>Confirmar Senha</label>

                    <input
                        type="password"
                        name="confirmar_senha"
                        placeholder="Repita sua senha"
                        required
                        minlength="6"
                        maxlength="12"
                    >
                </div>

                <div class="input_group">
                    <label>Telefone (Opcional)</label>

                    <input
                        type="text"
                        name="telefone"
                        placeholder="(11) 99999-9999"
                    >
                </div>

                <!-- Aceite obrigatório dos Termos de Uso -->
                <div class="termos_check">
                    <input
                        type="checkbox"
                        id="aceitarTermos"
                        name="aceitar_termos"
                        value="1"
                        required
                    >
                    <p>
                        Li e aceito os
                        <button type="button" class="termos_link" id="abrirTermos">Termos de uso</button>
                        da plataforma.
                    </p>
                </div>

                <button type="submit" class="cadastro_btn">
                    Criar Conta
                </button>

                <div class="divider">
                    <span>ou</span>
                </div>

                <button type="button" class="google_btn" onclick="window.location.href='google-callback.php'">
                    <svg class="google_icon" viewBox="0 0 48 48" width="20" height="20">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12
                            c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24
                            c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039
                            l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36
                            c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571
                            c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24
                            C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    Continuar com Google
                </button>

            </form>

            <div class="login_link">

                <p>
                    Já possui uma conta?
                </p>

                <a href="../front-end/login.php">
                    Entrar agora
                </a>

            </div>

        </div>

    </section>

</main>


<!-- ---------------- MODAL: TERMOS DE USO -------------------------- -->

<div class="modal" id="modalTermos" role="dialog" aria-modal="true" aria-labelledby="tituloTermos">

    <div class="modal-content">

        <div class="modal-header">

            <h2 id="tituloTermos">Termos de Uso</h2>

            <button
                type="button"
                class="close-modal"
                id="fecharTermos"
                aria-label="Fechar"
            >
                ×
            </button>

        </div>

        <div class="modal-body">

            <h3>1. Aceitação dos termos</h3>
            <p>
                Ao criar uma conta na plataforma AFDE, você declara que leu,
                compreendeu e concorda integralmente com estes Termos de Uso.
                O aceite é condição obrigatória para utilizar a plataforma.
            </p>

            <h3>2. Finalidade da plataforma</h3>
            <p>
                A AFDE é uma plataforma de educação financeira. Os conteúdos,
                simulações e ferramentas, incluindo a calculadora financeira,
                têm caráter exclusivamente educativo e informativo. Nada na
                plataforma constitui recomendação de investimento, consultoria
                financeira, jurídica ou tributária, nem promessa ou garantia de
                resultado.
            </p>

            <h3>3. Riscos financeiros</h3>
            <p>
                Toda decisão financeira envolve riscos. Algumas sugestões e
                exemplos apresentados, como investimentos em renda variável
                (ações, fundos imobiliários, criptoativos, entre outros), podem
                resultar em ganhos, mas também em perdas parciais ou totais do
                capital investido. Rentabilidade passada não garante resultados
                futuros. Os resultados das simulações são estimativas baseadas
                nos dados informados por você e podem diferir da realidade.
            </p>

            <h3>4. Responsabilidade do usuário</h3>
            <p>
                Você é o único responsável pelas decisões financeiras que tomar
                com base nas informações da plataforma e por avaliar se elas são
                adequadas ao seu perfil, objetivos e situação financeira. Antes
                de investir ou assumir qualquer compromisso financeiro, recomenda-se
                consultar um profissional qualificado. A AFDE não se responsabiliza
                por perdas, danos ou prejuízos decorrentes do uso das informações
                por conta própria pelo usuário, nos limites permitidos pela
                legislação aplicável.
            </p>

            <h3>5. Cadastro e segurança da conta</h3>
            <p>
                Você é responsável pela veracidade dos dados informados e pela
                guarda de sua senha. Não compartilhe suas credenciais com terceiros
                e avise-nos imediatamente em caso de uso não autorizado da sua conta.
            </p>

            <h3>6. Dados financeiros e dados bancários</h3>
            <p>
                A plataforma pode tratar informações financeiras e bancárias
                fornecidas por você. Informe apenas os dados necessários ao uso
                das ferramentas e nunca insira senhas bancárias, códigos de
                segurança, tokens ou outras credenciais de acesso a instituições
                financeiras. A AFDE não solicita esse tipo de informação por
                nenhum canal.
            </p>

            <h3>7. Privacidade e proteção de dados</h3>
            <p>
                Os dados pessoais e financeiros são tratados conforme a Lei Geral
                de Proteção de Dados (LGPD), com medidas de segurança técnicas e
                administrativas, e utilizados apenas para o funcionamento da
                plataforma. Você pode solicitar acesso, correção ou exclusão dos
                seus dados a qualquer momento. Nenhum sistema é totalmente imune
                a riscos, e a AFDE adota esforços razoáveis para protegê-los.
            </p>

            <h3>8. Uso adequado</h3>
            <p>
                É proibido utilizar a plataforma para fins ilícitos, tentar
                acessar áreas restritas, comprometer a segurança do sistema ou
                inserir dados de terceiros sem autorização.
            </p>

            <h3>9. Alterações</h3>
            <p>
                Estes termos podem ser atualizados a qualquer momento. O uso
                continuado da plataforma indica concordância com a versão vigente.
            </p>

        </div>

        <div class="modal-footer">
            <button type="button"  class="btn-recusar" id="recusarTermos">Fechar</button>
            <button type="button" class="btn-aceitar" id="aceitarTermosBtn">Li e aceito</button>
        </div>

    </div>

</div>


    <!-- Widget de Acessibilidade — integrado em todas as páginas -->
    <script src="../JS/acessibilidade.js" defer></script>
    <script src="../JS/liquid-glass.js?v=100" defer></script>


<script>
// ---------------- MODAL TERMOS DE USO -----------------

(function () {

    const modal       = document.getElementById('modalTermos');
    const btnAbrir    = document.getElementById('abrirTermos');
    const btnFechar   = document.getElementById('fecharTermos');
    const btnRecusar  = document.getElementById('recusarTermos');
    const btnAceitar  = document.getElementById('aceitarTermosBtn');
    const checkbox    = document.getElementById('aceitarTermos');

    function abrirModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        btnFechar.focus();
    }

    function fecharModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        btnAbrir.focus();
    }

    btnAbrir.addEventListener('click', abrirModal);
    btnFechar.addEventListener('click', fecharModal);
    btnRecusar.addEventListener('click', fecharModal);

    btnAceitar.addEventListener('click', function () {
        checkbox.checked = true;
        fecharModal();
    });

    // Fecha ao clicar fora da caixa
    modal.addEventListener('click', function (e) {
        if (e.target === modal) fecharModal();
    });

    // Fecha com ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            fecharModal();
        }
    });

})();
</script>
</body>
</html>