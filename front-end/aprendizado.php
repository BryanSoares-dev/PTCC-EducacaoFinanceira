<?php
// Inclui a conexão com o caminho correto
session_start();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Aprendizado</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* =========================
           ESTILOS ESPECÍFICOS DA PÁGINA DE APRENDIZADO
           (com fundo escuro e Liquid Glass)
        ========================= */

        /* Hero com fundo escuro (padrão do site) */
        .hero_aprendizado {
            padding-top: 120px;
            min-height: 60vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #0A2540 0%, #24314C 50%, #0A2540 100%);
            position: relative;
            overflow: hidden;
        }

        .hero_aprendizado::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(22, 226, 138, 0.06);
            right: -200px;
            top: -150px;
            filter: blur(120px);
        }

        .hero_aprendizado::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(22, 226, 138, 0.04);
            left: -150px;
            bottom: -100px;
            filter: blur(120px);
        }

        .hero_aprendizado_container {
            width: 100%;
            max-width: 1300px;
            margin: auto;
            padding: 60px 40px 40px;
            position: relative;
            z-index: 2;
        }

        .hero_aprendizado_content {
            max-width: 750px;
        }

        .badge_aprendizado {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 9999px;
            background: rgba(22, 226, 138, 0.12);
            color: #16E28A;
            border: 1px solid rgba(22, 226, 138, 0.25);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .hero_aprendizado_content h1 {
            font-size: clamp(2.8rem, 5vw, 4.2rem);
            line-height: 1.15;
            color: #ffffff;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero_aprendizado_content h1 .destaque {
            color: #16E28A;
            background: linear-gradient(135deg, #16E28A, #0db873);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero_aprendizado_content p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.15rem;
            line-height: 1.8;
            max-width: 600px;
            margin-bottom: 25px;
        }

        /* Botão menor para teste diagnóstico */
        .btn_teste_pequeno {
            display: inline-block;
            padding: 10px 24px;
            border: none;
            border-radius: 9999px;
            background: #16E28A;
            color: #0A2540;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 8px 20px rgba(22, 226, 138, 0.25);
            text-decoration: none;
        }

        .btn_teste_pequeno:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(22, 226, 138, 0.40);
        }

        .btn_teste_pequeno i {
            margin-right: 8px;
        }

        /* Grid de cards (sobre fundo escuro) */
        .cards_aprendizado {
            max-width: 1300px;
            margin: -40px auto 60px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            position: relative;
            z-index: 2;
        }

        .card_recurso {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: 35px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.20), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            text-align: center;
        }

        .card_recurso:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .card_icon {
            font-size: 3.5rem;
            color: #16E28A;
            margin-bottom: 20px;
            display: block;
            background: rgba(22, 226, 138, 0.10);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin-left: auto;
            margin-right: auto;
            transition: 0.3s;
        }

        .card_recurso:hover .card_icon {
            transform: scale(1.05);
            background: rgba(22, 226, 138, 0.18);
        }

        .card_recurso h3 {
            font-size: 1.8rem;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .card_recurso p {
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .btn_bloqueado {
            display: inline-block;
            padding: 14px 35px;
            border: none;
            border-radius: 9999px;
            background: #FF4757;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.25);
            opacity: 0.85;
        }

        .btn_bloqueado:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(255, 71, 87, 0.40);
            opacity: 1;
        }

        .btn_bloqueado i {
            margin-right: 8px;
        }

        /* =========================
           MODAL (POPUP) - Liquid Glass
        ========================= */
        .modal_overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            padding: 20px;
        }

        .modal_overlay.active {
            display: flex;
        }

        .modal_content {
            max-width: 500px;
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 28px;
            padding: 40px 35px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            text-align: center;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal_icon {
            font-size: 4rem;
            color: #FF4757;
            margin-bottom: 15px;
        }

        .modal_content h2 {
            font-size: 2rem;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .modal_content p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .modal_content .highlight {
            display: inline-block;
            background: rgba(255, 71, 87, 0.12);
            color: #FF4757;
            padding: 6px 16px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .modal_buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn_modal_primary {
            background: linear-gradient(135deg, #16E28A, #29f0a0);
            color: #0A2540;
            border: none;
            padding: 16px;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 8px 24px rgba(22, 226, 138, 0.30);
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        .btn_modal_primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(22, 226, 138, 0.45);
        }

        .btn_modal_secondary {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 600;
            cursor: pointer;
            padding: 10px;
            transition: 0.3s;
            width: 100%;
            text-align: center;
        }

        .btn_modal_secondary:hover {
            color: #ffffff;
        }

        /* =========================
           RESPONSIVO
        ========================= */
        @media (max-width: 768px) {
            .hero_aprendizado {
                min-height: 40vh;
                padding-top: 100px;
            }
            .hero_aprendizado_container {
                padding: 40px 20px;
            }
            .hero_aprendizado_content h1 {
                font-size: 2.2rem;
            }
            .cards_aprendizado {
                grid-template-columns: 1fr;
                margin-top: -20px;
            }
            .card_recurso {
                padding: 25px 20px;
            }
            .modal_content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<?php include_once 'navbar.php'; ?>

<main>

    <!-- HERO (fundo escuro) -->
    <section class="hero_aprendizado">
        <div class="hero_aprendizado_container">
            <div class="hero_aprendizado_content">
                <span class="badge_aprendizado">
                    <i class="fas fa-clipboard-check"></i> Avaliação
                </span>
                <h1>Realize o <span class="destaque">Teste Diagnóstico</span></h1>
                <p>Descubra seu perfil de investidor e desbloqueie videoaulas, exercícios diários e nossa loja exclusiva.</p>
                <!-- Botão menor para teste diagnóstico (link direto para o arquivo na mesma pasta) -->
                <a href="teste_diagnostico.php" class="btn_teste_pequeno">
                    <i class="fas fa-arrow-right"></i> Começar teste diagnóstico
                </a>
            </div>
        </div>
    </section>

    <!-- CARDS BLOQUEADOS -->
    <section class="cards_aprendizado">
        <!-- Card 1: Videoaulas -->
        <div class="card_recurso">
            <span class="card_icon"><i class="fas fa-video"></i></span>
            <h3>Videoaulas</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla convallis libero id justo tincidunt, sed venenatis lorem interdum.</p>
            <button class="btn_bloqueado" onclick="abrirModal('videoaulas')">
                <i class="fas fa-lock"></i> Acessar (bloqueado)
            </button>
        </div>

        <!-- Card 2: Exercícios Diários -->
        <div class="card_recurso">
            <span class="card_icon"><i class="fas fa-dumbbell"></i></span>
            <h3>Exercícios Diários</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla convallis libero id justo tincidunt, sed venenatis lorem interdum.</p>
            <button class="btn_bloqueado" onclick="abrirModal('exercicios')">
                <i class="fas fa-lock"></i> Acessar (bloqueado)
            </button>
        </div>

        <!-- Card 3: Loja -->
        <div class="card_recurso">
            <span class="card_icon"><i class="fas fa-store"></i></span>
            <h3>Loja</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla convallis libero id justo tincidunt, sed venenatis lorem interdum.</p>
            <button class="btn_bloqueado" onclick="abrirModal('loja')">
                <i class="fas fa-lock"></i> Acessar (bloqueado)
            </button>
        </div>
    </section>

    <!-- MODAL (POPUP) -->
    <div class="modal_overlay" id="modalTeste">
        <div class="modal_content">
            <div class="modal_icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h2>Teste Diagnóstico</h2>
            <span class="highlight">⏱️ 10 minutos</span>
            <p id="modalMensagem">Para desbloquear este recurso, você precisa realizar um teste diagnóstico rápido. Ele vai nos ajudar a personalizar sua experiência.</p>
            <div class="modal_buttons">
                <a href="teste_diagnostico.php" class="btn_modal_primary">
                    <i class="fas fa-arrow-right"></i> Realizar Teste
                </a>
                <button class="btn_modal_secondary" onclick="fecharModal()">
                    Agora não
                </button>
            </div>
        </div>
    </div>

</main>

<script>
    // Variável para armazenar o recurso que está sendo acessado
    var recursoAtual = '';

    // Abre o modal
    function abrirModal(recurso) {
        recursoAtual = recurso || 'recurso';
        var mensagem = document.getElementById('modalMensagem');
        
        // Personaliza a mensagem com base no recurso
        var nomes = {
            'videoaulas': 'videoaulas',
            'exercicios': 'exercícios diários',
            'loja': 'loja'
        };
        
        var nomeRecurso = nomes[recurso] || 'este recurso';
        mensagem.innerHTML = 'Para desbloquear <strong>' + nomeRecurso + '</strong>, você precisa realizar um teste diagnóstico rápido. Ele vai nos ajudar a personalizar sua experiência.';
        
        document.getElementById('modalTeste').classList.add('active');
        // Previne scroll da página
        document.body.style.overflow = 'hidden';
    }

    // Fecha o modal
    function fecharModal() {
        document.getElementById('modalTeste').classList.remove('active');
        // Restaura scroll da página
        document.body.style.overflow = 'auto';
    }

    // Fecha o modal ao clicar fora do conteúdo
    document.getElementById('modalTeste').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModal();
        }
    });

    // Fecha o modal com a tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModal();
        }
    });
</script>

</body>
</html>