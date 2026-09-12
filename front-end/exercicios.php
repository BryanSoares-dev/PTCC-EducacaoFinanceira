<?php
require_once __DIR__ . '/../back-end/bootstrap.php';
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Configuração simulada da ofensiva do usuário
$ofensiva_dias = isset($_SESSION['ofensiva_dias']) ? $_SESSION['ofensiva_dias'] : 3;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercícios & Treinos | Plataforma de Aprendizado</title>

    <link rel="stylesheet" href="../css/app.css">
    <link rel="icon" type="image/svg+xml" href="../img/favicon.svg">

    
</head>

<body>

<?php include_once 'navbar.php'; ?>

<main>

    <!-- ============================================================
         HERO SECTION
    ============================================================ -->
    <section class="hero-exercicios">
        <div class="hero-container">
            <div class="hero-left">
                <span class="badge-tag">
                    <i class="fas fa-brain"></i> Treinamento Prático
                </span>
                <h1>
                    🎯 Lista de <span class="destaque">Exercícios</span>
                </h1>
                <p>
                    Fixe o conhecimento das videoaulas com listas de 10 exercícios rápidos e dinâmicos no estilo Duolingo.
                </p>
            </div>

            <div class="hero-right">
                <!-- Botão Toggle Modo Escuro -->
                <button type="button" class="btn-theme-toggle" id="btnThemeToggle">
                    <i class="fas fa-moon" id="themeIcon"></i>
                    <span id="themeText">Modo Escuro</span>
                </button>

                <!-- Streak/Ofensiva Counter -->
                <div class="ofensiva-counter" id="headerOfensiva">
                    <i class="fas fa-fire"></i>
                    <span><strong id="diasOfensivaCount"><?php echo $ofensiva_dias; ?></strong> Dias de Ofensiva</span>
                </div>

                <a href="aprendizado.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================================
         SEÇÃO DE MÓDULOS (CARROSSEL)
    ============================================================ -->
    <section class="modulos-section" id="modulosSection">
        <div class="modulos-header">
            <h2>
                <i class="fas fa-layer-group"></i>
                Escolha o Módulo de Exercícios
            </h2>
            <span class="sub">
                <i class="fas fa-arrows-left-right"></i>
                Navegue e escolha uma trilha
            </span>
        </div>

        <div class="carousel-wrapper">
            <div class="carousel-track" id="modulosTrack">

                <!-- MÓDULO 1 -->
                <div class="modulo-card" data-modulo="1">
                    <span class="modulo-badge">Iniciante</span>
                    <div class="modulo-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3>Módulo 1</h3>
                    <p class="modulo-desc">Fundamentos do Investimento</p>
                    <p class="modulo-lorem">
                        Aprenda conceitos básicos e exercite o primeiro passo no mundo financeiro.
                    </p>
                    <div class="modulo-aulas-count">
                        <i class="fas fa-list-check"></i>
                        4 Listas Práticas
                    </div>
                    <button type="button" class="btn-entrar" data-modulo="1">
                        Ver Exercícios
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- MÓDULO 2 -->
                <div class="modulo-card" data-modulo="2">
                    <span class="modulo-badge">Intermediário</span>
                    <div class="modulo-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Módulo 2</h3>
                    <p class="modulo-desc">Análise Técnica</p>
                    <p class="modulo-lorem">
                        Teste seus conhecimentos na leitura de gráficos, padrões e tendências.
                    </p>
                    <div class="modulo-aulas-count">
                        <i class="fas fa-list-check"></i>
                        4 Listas Práticas
                    </div>
                    <button type="button" class="btn-entrar" data-modulo="2">
                        Ver Exercícios
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- MÓDULO 3 -->
                <div class="modulo-card" data-modulo="3">
                    <span class="modulo-badge">Avançado</span>
                    <div class="modulo-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3>Módulo 3</h3>
                    <p class="modulo-desc">Fundamentos de Valuation</p>
                    <p class="modulo-lorem">
                        Avalie empresas com balanços reais e simulações de múltiplos.
                    </p>
                    <div class="modulo-aulas-count">
                        <i class="fas fa-list-check"></i>
                        4 Listas Práticas
                    </div>
                    <button type="button" class="btn-entrar" data-modulo="3">
                        Ver Exercícios
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- MÓDULO 4 -->
                <div class="modulo-card" data-modulo="4">
                    <span class="modulo-badge">Expert</span>
                    <div class="modulo-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Módulo 4</h3>
                    <p class="modulo-desc">Estratégias Avançadas</p>
                    <p class="modulo-lorem">
                        Derivativos, opções e hedge colocados à prova de forma simples.
                    </p>
                    <div class="modulo-aulas-count">
                        <i class="fas fa-list-check"></i>
                        4 Listas Práticas
                    </div>
                    <button type="button" class="btn-entrar" data-modulo="4">
                        Ver Exercícios
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- MÓDULO 5 -->
                <div class="modulo-card" data-modulo="5">
                    <span class="modulo-badge">Bônus</span>
                    <div class="modulo-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3>Módulo 5</h3>
                    <p class="modulo-desc">Cases e Simulações</p>
                    <p class="modulo-lorem">
                        Desafios práticos baseados em cenários reais do mercado global.
                    </p>
                    <div class="modulo-aulas-count">
                        <i class="fas fa-list-check"></i>
                        3 Listas Práticas
                    </div>
                    <button type="button" class="btn-entrar" data-modulo="5">
                        Ver Exercícios
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

            </div>

            <button type="button" class="carousel-btn prev" id="prevBtn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="carousel-btn next" id="nextBtn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- ============================================================
         LISTA DE EXERCÍCIOS VINCULADOS ÀS VIDEOAULAS
    ============================================================ -->
    <section class="timeline-container" id="timelineContainer">
        <div class="timeline-header">
            <h2>
                <i class="fas fa-tasks"></i>
                <span id="moduloTitulo">Módulo 1 - Exercícios</span>
            </h2>

            <button type="button" class="btn-voltar" id="btnVoltarModulos">
                <i class="fas fa-arrow-left"></i>
                Voltar aos Módulos
            </button>
        </div>

        <div class="aulas-lista" id="exerciciosLista">
            <!-- Injetado via JS -->
        </div>
    </section>

</main>

<!-- ============================================================
     MODAL / POPUP ESTILO DUOLINGO DE EXERCÍCIOS
============================================================ -->
<div class="quiz-modal" id="quizModal">
    <div class="quiz-card">
        <div class="quiz-header">
            <button class="fechar-quiz" id="fecharQuiz">
                <i class="fas fa-times"></i>
            </button>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" id="quizProgressFill"></div>
            </div>
            <span class="quiz-step-count" id="quizStepCount">1/10</span>
        </div>

        <div class="quiz-body">
            <div class="quiz-pergunta-meta">
                <i class="fas fa-bolt"></i>
                <span id="quizMetaAula">Atribuído à Videoaula: Introdução ao Mundo dos Investimentos</span>
            </div>

            <h3 class="quiz-pergunta" id="quizPerguntaText">
                Qual é a principal função de um ativo de Renda Fixa no seu portfólio inicial?
            </h3>

            <div class="options-grid" id="quizOptionsGrid">
                <!-- Opções injetadas via JavaScript -->
            </div>
        </div>

        <div class="quiz-footer">
            <span style="color: var(--texto-terciario); font-size: 0.85rem;">
                <i class="fas fa-shield-halved" style="color: var(--verde);"></i> Lista de 10 Questões Rápidas
            </span>
            <button class="btn-responder" id="btnResponderQuiz" disabled>
                Verificar
            </button>
        </div>
    </div>
</div>

<!-- ============================================================
     POPUP DE LIGAÇÃO DE OFENSIVA (STREAK POPUP)
============================================================ -->
<div class="streak-modal-overlay" id="streakModal">
    <div class="streak-modal-card">
        <div class="fire-anim-box">
            <i class="fas fa-fire"></i>
        </div>
        <h2><span class="highlight-fogo">Ofensiva</span> Ligada!</h2>
        <div class="streak-days-badge" id="modalStreakDays">
            🔥 4 Dias Seguidos!
        </div>
        <p>
            Parabéns! Você concluiu a lista de exercícios de hoje e manteve sua chama acesa. Volte amanhã para aumentar ainda mais sua ofensiva!
        </p>
        <button class="btn-streak-continuar" onclick="fecharStreakModal()">
            Incrível! Continuar
        </button>
    </div>
</div>

<script>
/* ================================================================
   TOGGLE DO MODO ESCURO COM PERSISTÊNCIA (LOCALSTORAGE)
================================================================ */
const btnThemeToggle = document.getElementById("btnThemeToggle");
const themeIcon = document.getElementById("themeIcon");
const themeText = document.getElementById("themeText");

function applyTheme(isDark) {
    if (isDark) {
        document.body.classList.add("dark-mode");
        themeIcon.className = "fas fa-sun";
        themeText.textContent = "Modo Claro";
    } else {
        document.body.classList.remove("dark-mode");
        themeIcon.className = "fas fa-moon";
        themeText.textContent = "Modo Escuro";
    }
}

// Verifica tema salvo ou preferência do sistema
const savedTheme = localStorage.getItem("theme");
if (savedTheme) {
    applyTheme(savedTheme === "dark");
}

btnThemeToggle.addEventListener("click", () => {
    const isDark = document.body.classList.toggle("dark-mode");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    applyTheme(isDark);
});

/* ================================================================
   DADOS DOS EXERCÍCIOS VINCULADOS ÀS VIDEOAULAS & ESTIMATIVA DE TEMPO
================================================================ */
const exerciciosPorModulo = {
    1: [
        {
            titulo: "Exercício 01: Conceitos de Renda Fixa vs Variável",
            videoAula: "Introdução ao Mundo dos Investimentos",
            duracaoVideo: "12:30",
            tempoMedioLista: "4 min",
            resumo: "Lista de 10 questões rápidas sobre definições básicas de liquidez, rentabilidade e segurança de ativos iniciais."
        },
        {
            titulo: "Exercício 02: Classificação de Ativos e Títulos",
            videoAula: "Tipos de Ativos Financeiros",
            duracaoVideo: "18:45",
            tempoMedioLista: "5 min",
            resumo: "Identifique a diferença entre Tesouro Direto, CDBs, Ações e Fundos Imobiliários em perguntas interativas."
        },
        {
            titulo: "Exercício 03: Cálculo de Risco e Volatilidade",
            videoAula: "Risco e Retorno",
            duracaoVideo: "22:10",
            tempoMedioLista: "6 min",
            resumo: "Questões práticas avaliando a relação de proporcionalidade entre risco assumido e retorno esperado."
        },
        {
            titulo: "Exercício 04: Diversificação Prática",
            videoAula: "Montando sua Carteira Inicial",
            duracaoVideo: "15:20",
            tempoMedioLista: "5 min",
            resumo: "Aprenda a simular a distribuição percentual de recursos para mitigar riscos desnecessários."
        }
    ],
    2: [
        {
            titulo: "Exercício 05: Leitura de Candlestick",
            videoAula: "Gráficos e Tendências",
            duracaoVideo: "14:50",
            tempoMedioLista: "5 min",
            resumo: "Interprete padrões de abertura, fechamento, máximas e mínimas em candles operacionais."
        },
        {
            titulo: "Exercício 06: Osciladores e Médias Móveis",
            videoAula: "Indicadores Técnicos",
            duracaoVideo: "20:10",
            tempoMedioLista: "6 min",
            resumo: "Identifique sinais de compra e venda usando IFR, MACD e cruzamento de médias."
        },
        {
            titulo: "Exercício 07: Marcação de Suportes e Resistências",
            videoAula: "Suporte e Resistência",
            duracaoVideo: "17:30",
            tempoMedioLista: "4 min",
            resumo: "Exercícios visuais para identificar zonas psicologicamente relevantes de preço."
        },
        {
            titulo: "Exercício 08: Padrões de Reversão de Tendência",
            videoAula: "Padrões de Candlestick",
            duracaoVideo: "25:00",
            tempoMedioLista: "6 min",
            resumo: "Reconheça figuras como OCO, Engolfo e Martelo em 10 testes rápidos."
        }
    ],
    3: [
        {
            titulo: "Exercício 09: Múltiplos P/L e P/VP",
            videoAula: "O que é Valuation?",
            duracaoVideo: "16:40",
            tempoMedioLista: "5 min",
            resumo: "Compare empresas do mesmo setor analisando valuation simplificado."
        },
        {
            titulo: "Exercício 10: Taxa de Desconto e WACC",
            videoAula: "Fluxo de Caixa Descontado",
            duracaoVideo: "22:30",
            tempoMedioLista: "7 min",
            resumo: "Cálculos interativos de valor presente líquido aplicados a exercícios ágeis."
        },
        {
            titulo: "Exercício 11: Benchmark e EV/EBITDA",
            videoAula: "Múltiplos de Mercado",
            duracaoVideo: "19:15",
            tempoMedioLista: "5 min",
            resumo: "Avaliação da eficiência operacional e endividamento corporativo."
        },
        {
            titulo: "Exercício 12: Análise de Balanços e DRE",
            videoAula: "Análise de Empresas",
            duracaoVideo: "21:00",
            tempoMedioLista: "6 min",
            resumo: "Aprenda a encontrar margem bruta, líquida e Ebitda em demonstrativos simulados."
        }
    ],
    4: [
        {
            titulo: "Exercício 13: Calls e Puts na Prática",
            videoAula: "Derivativos e Opções",
            duracaoVideo: "18:20",
            tempoMedioLista: "6 min",
            resumo: "Entenda direitos e obrigações no mercado de opções com alternativas diretas."
        },
        {
            titulo: "Exercício 14: Estruturas de Proteção",
            videoAula: "Estratégias de Hedge",
            duracaoVideo: "23:10",
            tempoMedioLista: "6 min",
            resumo: "Monte estratégias de trava de alta e proteção de carteira contra queda."
        },
        {
            titulo: "Exercício 15: Impactos de Juros e Inflação",
            videoAula: "Análise Macro e Micro",
            duracaoVideo: "20:45",
            tempoMedioLista: "5 min",
            resumo: "Avalie a influência da Taxa Selic e IPCA sobre a rentabilidade dos investimentos."
        },
        {
            titulo: "Exercício 16: Rebalanceamento de Carteira",
            videoAula: "Planejamento de Longo Prazo",
            duracaoVideo: "16:30",
            tempoMedioLista: "4 min",
            resumo: "Simule a manutenção das proporções ideais ao longo dos anos."
        }
    ],
    5: [
        {
            titulo: "Exercício 17: Análise de Cases Real: Small Caps",
            videoAula: "Estudo de Caso: Small Caps",
            duracaoVideo: "14:20",
            tempoMedioLista: "5 min",
            resumo: "Exercícios de tomada de decisão com empresas de alto potencial de crescimento."
        },
        {
            titulo: "Exercício 18: Simulação de Portfólio Resiliente",
            videoAula: "Simulação de Carteira",
            duracaoVideo: "19:40",
            tempoMedioLista: "5 min",
            resumo: "Defenda seu patrimônio em cenários de estresse econômico simulado."
        },
        {
            titulo: "Exercício 19: Teste de Stress e Value at Risk",
            videoAula: "Análise de Risco Avançada",
            duracaoVideo: "22:10",
            tempoMedioLista: "6 min",
            resumo: "Calcule a perda máxima potencial estimada para um portfólio."
        }
    ]
};

/* BANCO DE QUESTÕES ESTILO DUOLINGO PARA O MODAL */
const questoesDuolingo = [
    {
        pergunta: "1. O que acontece com o preço de um título de Renda Fixa pré-fixado quando a taxa de juros do mercado sobe?",
        opcoes: [
            "O preço do título diminui (Marcação a Mercado)",
            "O preço do título aumenta na mesma proporção",
            "O rendimento é cancelado pelo Banco Central",
            "Não há nenhum impacto no valor do título"
        ],
        correta: 0
    },
    {
        pergunta: "2. Qual desses ativos é considerado de maior risco e volatilidade?",
        opcoes: [
            "Tesouro SELIC",
            "CDB de grande banco com FGC",
            "Ações de Small Caps",
            "Título Público Pós-fixado"
        ],
        correta: 2
    },
    {
        pergunta: "3. O Fundo Garantidor de Crédito (FGC) garante investimentos em CDB até qual limite por CPF e instituição?",
        opcoes: [
            "R$ 100.000",
            "R$ 250.000",
            "R$ 500.000",
            "Garantia ilimitada"
        ],
        correta: 1
    },
    {
        pergunta: "4. Na Análise Técnica, o que indica um padrão de Candlestick conhecido como 'Martelo' no fundo de uma tendência?",
        opcoes: [
            "Forte sinal de continuação da queda",
            "Possível reversão para tendência de alta",
            "Estagnação indefinida do mercado",
            "Necessidade de venda imediata"
        ],
        correta: 1
    },
    {
        pergunta: "5. O indicador P/L (Preço sobre Lucro) de uma ação indica:",
        opcoes: [
            "A porcentagem de dividendos pagos ao ano",
            "O tempo em anos para reaver o capital investido através dos lucros",
            "O valor patrimonial da empresa na bolsa",
            "O faturamento bruto da companhia"
        ],
        correta: 1
    },
    {
        pergunta: "6. O que representa uma opção do tipo 'CALL' no mercado financeiro?",
        opcoes: [
            "O dever de vender uma ação no futuro",
            "O direito de comprar uma ação por um preço determinado",
            "O direito de vender uma ação por qualquer valor",
            "Um empréstimo garantido por ações"
        ],
        correta: 1
    },
    {
        pergunta: "7. O que é a diversificação de carteira segundo a teoria moderna das finanças?",
        opcoes: [
            "Comprar 10 ações do mesmo setor elétrico",
            "Investir todo o capital no ativo de maior rendimento recente",
            "Alocar recursos em diferentes classes de ativos desalinhados entre si",
            "Manter todo o dinheiro em poupança"
        ],
        correta: 2
    },
    {
        pergunta: "8. Qual a principal característica do Tesouro IPCA+?",
        opcoes: [
            "Protege contra a inflação garantindo ganho real acima do IPCA",
            "Paga uma taxa fixa sem correção inflacionária",
            "Acompanha exatamente a oscilação do Dólar",
            "É isento de Imposto de Renda"
        ],
        correta: 0
    },
    {
        pergunta: "9. Em relação aos Fundos Imobiliários (FIIs), qual é a periodicidade comum do pagamento de rendimentos aos cotistas?",
        opcoes: [
            "Anual",
            "Trimestral",
            "Mensal",
            "Apenas no resgate das cotas"
        ],
        correta: 2
    },
    {
        pergunta: "10. O que significa o conceito de Liquidez em um investimento?",
        opcoes: [
            "A capacidade do ativo gerar dividendos elevados",
            "A rapidez com que se consegue converter o investimento em dinheiro sem grande perda de valor",
            "A garantia do FGC no caso de falência",
            "A isenção total de taxas operacionais"
        ],
        correta: 1
    }
];

/* ================================================================
   CARROSSEL DE MÓDULOS
================================================================ */
const track = document.getElementById("modulosTrack");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
let currentIndex = 0;

function getCardWidth() {
    const card = track.querySelector(".modulo-card");
    return card ? card.offsetWidth + 24 : 0;
}

function updateCarousel() {
    const cards = track.querySelectorAll(".modulo-card");
    if (!cards.length) return;

    const cardWidth = getCardWidth();
    const wrapper = track.parentElement.offsetWidth;
    const totalWidth = cards.length * cardWidth - 24;
    const maxOffset = Math.max(0, totalWidth - wrapper);

    let offset = currentIndex * cardWidth;
    offset = Math.min(offset, maxOffset);

    track.style.transform = `translateX(-${offset}px)`;
}

prevBtn.addEventListener("click", () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateCarousel();
    }
});

nextBtn.addEventListener("click", () => {
    const cards = track.querySelectorAll(".modulo-card");
    if (currentIndex < cards.length - 1) {
        currentIndex++;
        updateCarousel();
    }
});

window.addEventListener("resize", updateCarousel);

/* ================================================================
   NAVEGAÇÃO ENTRE MÓDULOS E EXERCÍCIOS
================================================================ */
const modulosSection = document.getElementById("modulosSection");
const timelineContainer = document.getElementById("timelineContainer");
const exerciciosLista = document.getElementById("exerciciosLista");
const moduloTitulo = document.getElementById("moduloTitulo");

function abrirModulo(moduloId) {
    const card = document.querySelector(`.modulo-card[data-modulo="${moduloId}"]`);
    if (!card) return;

    const nome = card.querySelector("h3").textContent;
    const desc = card.querySelector(".modulo-desc").textContent;
    moduloTitulo.textContent = `${nome} - ${desc}`;

    const exercicios = exerciciosPorModulo[moduloId] || [];
    exerciciosLista.innerHTML = "";

    exercicios.forEach((item, index) => {
        const div = document.createElement("div");
        div.className = "aula-item";

        div.innerHTML = `
            <div class="aula-info">
                <span class="aula-titulo">${item.titulo}</span>
                <div class="aula-meta">
                    <span>
                        <i class="fas fa-circle-play"></i>
                        Aula Vinculada: <strong class="vinculo-aula">${item.videoAula}</strong>
                    </span>
                    <span>
                        <i class="fas fa-clock"></i>
                        Duração do Vídeo: ${item.duracaoVideo}
                    </span>
                </div>
            </div>

            <div class="aula-duracao" title="Tempo médio estimado para realizar os 10 exercícios">
                <i class="fas fa-stopwatch"></i>
                ~${item.tempoMedioLista} de lista
            </div>

            <button type="button" class="btn-praticar" data-modulo="${moduloId}" data-index="${index}">
                <i class="fas fa-play"></i>
                Fazer Exercício
            </button>

            <!-- Tooltip Liquid Glass ao passar o mouse -->
            <div class="tooltip">
                <div class="tt-title">
                    <span>${item.titulo}</span>
                    <i class="fas fa-bolt" style="color:var(--verde);"></i>
                </div>
                <div class="tt-meta">
                    <span>
                        <i class="fas fa-video"></i>
                        Atribuído à Videoaula: <strong>${item.videoAula}</strong>
                    </span>
                    <span>
                        <i class="fas fa-stopwatch"></i>
                        Tempo estimado para responder: <strong>${item.tempoMedioLista}</strong>
                    </span>
                    <span>
                        <i class="fas fa-list-check"></i>
                        Formato: <strong>10 Questões Rápidas (Duolingo Style)</strong>
                    </span>
                </div>
                <div class="tt-resumo">
                    ${item.resumo}
                </div>
            </div>
        `;

        exerciciosLista.appendChild(div);
    });

    modulosSection.style.display = "none";
    timelineContainer.classList.add("active");

    window.scrollTo({
        top: timelineContainer.offsetTop - 30,
        behavior: "smooth"
    });
}

document.querySelectorAll(".btn-entrar").forEach(btn => {
    btn.addEventListener("click", function() {
        const modulo = Number(this.dataset.modulo);
        abrirModulo(modulo);
    });
});

document.getElementById("btnVoltarModulos").addEventListener("click", () => {
    timelineContainer.classList.remove("active");
    modulosSection.style.display = "block";
    window.scrollTo({ top: 0, behavior: "smooth" });
});

/* ================================================================
   SISTEMA DE QUIZ INTERATIVO (DUOLINGO 10 QUESTÕES)
================================================================ */
const quizModal = document.getElementById("quizModal");
const quizProgressFill = document.getElementById("quizProgressFill");
const quizStepCount = document.getElementById("quizStepCount");
const quizMetaAula = document.getElementById("quizMetaAula");
const quizPerguntaText = document.getElementById("quizPerguntaText");
const quizOptionsGrid = document.getElementById("quizOptionsGrid");
const btnResponderQuiz = document.getElementById("btnResponderQuiz");
const fecharQuiz = document.getElementById("fecharQuiz");

let currentQuestionIndex = 0;
let selectedOptionIndex = null;
let currentExerciseMeta = null;

function iniciarQuiz(itemData) {
    currentQuestionIndex = 0;
    selectedOptionIndex = null;
    currentExerciseMeta = itemData;

    quizMetaAula.textContent = `Atribuído à Videoaula: ${itemData.videoAula}`;
    carregarPergunta(currentQuestionIndex);
    quizModal.classList.add("active");
}

function carregarPergunta(index) {
    const q = questoesDuolingo[index];
    selectedOptionIndex = null;
    btnResponderQuiz.disabled = true;
    btnResponderQuiz.textContent = index === 9 ? "Finalizar Lista" : "Verificar";

    // Progress Bar
    const pct = ((index + 1) / 10) * 100;
    quizProgressFill.style.width = `${pct}%`;
    quizStepCount.textContent = `${index + 1}/10`;

    quizPerguntaText.textContent = q.pergunta;
    quizOptionsGrid.innerHTML = "";

    const letras = ["A", "B", "C", "D"];
    q.opcoes.forEach((optText, optIdx) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "option-btn";
        btn.innerHTML = `
            <span class="option-letter">${letras[optIdx]}</span>
            <span>${optText}</span>
        `;

        btn.addEventListener("click", () => {
            document.querySelectorAll(".option-btn").forEach(b => b.classList.remove("selected"));
            btn.classList.add("selected");
            selectedOptionIndex = optIdx;
            btnResponderQuiz.disabled = false;
        });

        quizOptionsGrid.appendChild(btn);
    });
}

btnResponderQuiz.addEventListener("click", () => {
    if (selectedOptionIndex === null) return;

    const q = questoesDuolingo[currentQuestionIndex];
    const options = quizOptionsGrid.querySelectorAll(".option-btn");

    // Marcação visual de correto e errado
    options.forEach((optBtn, idx) => {
        if (idx === q.correta) {
            optBtn.classList.add("correct");
        } else if (idx === selectedOptionIndex) {
            optBtn.classList.add("wrong");
        }
        optBtn.disabled = true;
    });

    setTimeout(() => {
        if (currentQuestionIndex < 9) {
            currentQuestionIndex++;
            carregarPergunta(currentQuestionIndex);
        } else {
            // FIM DA LISTA DE 10 EXERCÍCIOS - LIGA A OFENSIVA!
            fecharQuizModal();
            ativarOfensiva();
        }
    }, 1100);
});

function fecharQuizModal() {
    quizModal.classList.remove("active");
}

fecharQuiz.addEventListener("click", fecharQuizModal);

/* Event Delegation para abrir o quiz a partir do botão 'Fazer Exercício' */
document.addEventListener("click", (e) => {
    const btn = e.target.closest(".btn-praticar");
    if (!btn) return;

    const modulo = Number(btn.dataset.modulo);
    const index = Number(btn.dataset.index);
    const itemData = exerciciosPorModulo[modulo][index];

    iniciarQuiz(itemData);
});

/* ================================================================
   SISTEMA DE POPUP DE OFENSIVA (STREAK FIRE POPUP)
================================================================ */
const streakModal = document.getElementById("streakModal");
const diasOfensivaCount = document.getElementById("diasOfensivaCount");
const modalStreakDays = document.getElementById("modalStreakDays");

function ativarOfensiva() {
    // Incrementa a ofensiva para efeito visual
    let atual = parseInt(diasOfensivaCount.textContent) || 0;
    let novoValor = atual + 1;

    diasOfensivaCount.textContent = novoValor;
    modalStreakDays.textContent = `🔥 ${novoValor} Dias Seguidos!`;

    // Exibe o popup animado com o foguinho
    streakModal.classList.add("active");
}

function fecharStreakModal() {
    streakModal.classList.remove("active");
}

/* Inicialização */
window.addEventListener("load", () => {
    updateCarousel();
});
</script>

</body>
</html>
