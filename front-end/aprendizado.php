<?php
// Inclui a conexão com o caminho correto
require_once __DIR__ . '/../back-end/bootstrap.php';
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
    <link rel="stylesheet" href="../css/app.css">
    <link rel="icon" type="image/svg+xml" href="../img/favicon.svg">
    
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

    <!-- ============================================ -->
    <!-- PRIMEIRO MODAL: solicitação do teste         -->
    <!-- ============================================ -->
    <div class="modal_overlay" id="modalTeste">
        <div class="modal_content">
            <div class="modal_icon" style="color: #16E28A;">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h2>Teste Diagnóstico</h2>
            <span class="highlight">⏱️ 10 minutos</span>
            <p id="modalMensagem">Para desbloquear este recurso, você precisa realizar um teste diagnóstico rápido. Ele vai nos ajudar a personalizar sua experiência.</p>
            <div class="modal_buttons">
                <a href="teste_diagnostico.php" class="btn_modal_primary">
                    <i class="fas fa-arrow-right"></i> Realizar Teste
                </a>
                <button class="btn_modal_secondary" onclick="pularTeste()">
                    Pular Teste
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SEGUNDO MODAL: aviso sobre classificação     -->
    <!-- ============================================ -->
    <div class="modal_overlay" id="modalAviso">
        <div class="modal_content">
            <div class="modal_icon modal_icon_warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2>Atenção!</h2>
            <p>
                Você optou por <strong>pular o teste diagnóstico</strong>.<br><br>
                Como não temos como avaliar seu conhecimento, você será automaticamente classificado como 
                <strong style="color: #FFA502;">Ferro 1</strong> (nível mais baixo).<br><br>
                Com essa patente, você <strong>não terá acesso</strong> a videoaulas e exercícios de níveis superiores, 
                devendo começar do nível básico.
            </p>
            <div class="modal_buttons">
                <!-- Botão para voltar ao primeiro modal -->
                <button class="btn_modal_secondary" onclick="voltarTeste()" style="color: #16E28A; border: 1px solid rgba(22,226,138,0.3); border-radius: 9999px; padding: 12px;">
                    <i class="fas fa-arrow-left"></i> Voltar e fazer o teste
                </button>
                <!-- Botão para confirmar e seguir com nível baixo -->
                <button class="btn_modal_primary" onclick="confirmarPular()" style="background: #FFA502; box-shadow: 0 8px 24px rgba(255, 165, 2, 0.3);">
                    <i class="fas fa-check"></i> Entendi, quero começar do nível baixo
                </button>
            </div>
        </div>
    </div>

</main>

<script>
    // Variável para armazenar o recurso que está sendo acessado
    var recursoAtual = '';

    // Abre o primeiro modal (solicitação de teste)
    function abrirModal(recurso) {
        recursoAtual = recurso || 'recurso';
        var mensagem = document.getElementById('modalMensagem');
        
        var nomes = {
            'videoaulas': 'videoaulas',
            'exercicios': 'exercícios diários',
            'loja': 'loja'
        };
        
        var nomeRecurso = nomes[recurso] || 'este recurso';
        mensagem.innerHTML = 'Para desbloquear <strong>' + nomeRecurso + '</strong>, você precisa realizar um teste diagnóstico rápido. Ele vai nos ajudar a personalizar sua experiência.';
        
        document.getElementById('modalTeste').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Fecha o primeiro modal
    function fecharModal() {
        document.getElementById('modalTeste').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Função chamada ao clicar em "Pular Teste" (abre o segundo modal)
    function pularTeste() {
        fecharModal();               // fecha o primeiro modal
        document.getElementById('modalAviso').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Função para voltar do segundo modal para o primeiro
    function voltarTeste() {
        document.getElementById('modalAviso').classList.remove('active');
        // Reabre o primeiro modal com a mensagem personalizada
        abrirModal(recursoAtual);    // reutiliza o recurso armazenado
    }

    // Fecha o segundo modal e confirma a classificação como Ferro 1
    function confirmarPular() {
        document.getElementById('modalAviso').classList.remove('active');
        document.body.style.overflow = 'auto';
        // Aqui você pode redirecionar para a página de conteúdo básico, se desejar
        // Exemplo: window.location.href = 'conteudo_basico.php';
        // Ou recarregar a página para refletir nova classificação
        // window.location.reload();
    }

    // Fecha qualquer modal ao clicar fora do conteúdo
    document.querySelectorAll('.modal_overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'modalTeste') {
                    fecharModal();
                } else if (this.id === 'modalAviso') {
                    // Fecha o segundo sem ação
                    this.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            }
        });
    });

    // Fecha com a tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('modalAviso').classList.contains('active')) {
                document.getElementById('modalAviso').classList.remove('active');
                document.body.style.overflow = 'auto';
            } else if (document.getElementById('modalTeste').classList.contains('active')) {
                fecharModal();
            }
        }
    });
</script>

</body>
</html>
