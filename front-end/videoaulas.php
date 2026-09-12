<?php
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

    <title>Videoaulas</title>

    <link rel="stylesheet" href="../css/app.css">

    <link rel="stylesheet"

    <link rel="icon" type="image/svg+xml" href="../img/favicon.svg">

    

</head>


<body>


<?php include_once 'navbar.php'; ?>


<main>


    <!-- ============================================================
         HERO
    ============================================================ -->

    <section class="hero-videoaulas">

        <div class="hero-container">

            <div class="hero-left">

                <h1>
                    📚
                    <span class="destaque">
                        Videoaulas
                    </span>
                </h1>

                <p>
                    Aprenda no seu ritmo com conteúdos exclusivos,
                    organizados para facilitar sua jornada de aprendizado.
                </p>

            </div>


            <div class="hero-right">

                <div class="hero-stats">

                    <span>
                        <i class="fas fa-film"></i>
                        19 aulas
                    </span>

                    <span>
                        <i class="fas fa-layer-group"></i>
                        5 módulos
                    </span>

                    <span>
                        <i class="fas fa-clock"></i>
                        5h20 total
                    </span>

                </div>


                <a
                    href="loja.php"
                    class="btn-voltar">

                    <i class="fas fa-arrow-left"></i>

                    Voltar

                </a>

            </div>

        </div>

    </section>


    <!-- ============================================================
         MÓDULOS
    ============================================================ -->

    <section
        class="modulos-section"
        id="modulosSection">


        <div class="modulos-header">

            <h2>

                <i class="fas fa-th-large"></i>

                Escolha seu módulo

            </h2>


            <span class="sub">

                <i class="fas fa-arrows-left-right"></i>

                Navegue pelos módulos

            </span>

        </div>


        <div class="carousel-wrapper">


            <div
                class="carousel-track"
                id="modulosTrack">


                <!-- ==================================================
                     MÓDULO 1
                ================================================== -->

                <div
                    class="modulo-card"
                    data-modulo="1">

                    <span class="modulo-badge">
                        Iniciante
                    </span>


                    <div class="modulo-icon">

                        <i class="fas fa-seedling"></i>

                    </div>


                    <h3>
                        Módulo 1
                    </h3>


                    <p class="modulo-desc">
                        Fundamentos do Investimento
                    </p>


                    <p class="modulo-lorem">
                        Lorem ipsum dolor sit amet,
                        consectetur adipiscing elit.
                        Integer vitae turpis.
                    </p>


                    <div class="modulo-aulas-count">

                        <i class="fas fa-play-circle"></i>

                        4 aulas

                    </div>


                    <button
                        type="button"
                        class="btn-entrar"
                        data-modulo="1">

                        Entrar

                        <i class="fas fa-arrow-right"></i>

                    </button>

                </div>


                <!-- ==================================================
                     MÓDULO 2
                ================================================== -->

                <div
                    class="modulo-card"
                    data-modulo="2">

                    <span class="modulo-badge">
                        Intermediário
                    </span>


                    <div class="modulo-icon">

                        <i class="fas fa-chart-line"></i>

                    </div>


                    <h3>
                        Módulo 2
                    </h3>


                    <p class="modulo-desc">
                        Análise Técnica
                    </p>


                    <p class="modulo-lorem">
                        Lorem ipsum dolor sit amet,
                        consectetur adipiscing elit.
                        Sed do eiusmod tempor.
                    </p>


                    <div class="modulo-aulas-count">

                        <i class="fas fa-play-circle"></i>

                        4 aulas

                    </div>


                    <button
                        type="button"
                        class="btn-entrar"
                        data-modulo="2">

                        Entrar

                        <i class="fas fa-arrow-right"></i>

                    </button>

                </div>


                <!-- ==================================================
                     MÓDULO 3
                ================================================== -->

                <div
                    class="modulo-card"
                    data-modulo="3">

                    <span class="modulo-badge">
                        Avançado
                    </span>


                    <div class="modulo-icon">

                        <i class="fas fa-balance-scale"></i>

                    </div>


                    <h3>
                        Módulo 3
                    </h3>


                    <p class="modulo-desc">
                        Fundamentos de Valuation
                    </p>


                    <p class="modulo-lorem">
                        Lorem ipsum dolor sit amet,
                        consectetur adipiscing elit.
                        Duis aute irure dolor.
                    </p>


                    <div class="modulo-aulas-count">

                        <i class="fas fa-play-circle"></i>

                        4 aulas

                    </div>


                    <button
                        type="button"
                        class="btn-entrar"
                        data-modulo="3">

                        Entrar

                        <i class="fas fa-arrow-right"></i>

                    </button>

                </div>


                <!-- ==================================================
                     MÓDULO 4
                ================================================== -->

                <div
                    class="modulo-card"
                    data-modulo="4">

                    <span class="modulo-badge">
                        Expert
                    </span>


                    <div class="modulo-icon">

                        <i class="fas fa-rocket"></i>

                    </div>


                    <h3>
                        Módulo 4
                    </h3>


                    <p class="modulo-desc">
                        Estratégias Avançadas
                    </p>


                    <p class="modulo-lorem">
                        Lorem ipsum dolor sit amet,
                        consectetur adipiscing elit.
                        Excepteur sint occaecat.
                    </p>


                    <div class="modulo-aulas-count">

                        <i class="fas fa-play-circle"></i>

                        4 aulas

                    </div>


                    <button
                        type="button"
                        class="btn-entrar"
                        data-modulo="4">

                        Entrar

                        <i class="fas fa-arrow-right"></i>

                    </button>

                </div>


                <!-- ==================================================
                     MÓDULO 5
                ================================================== -->

                <div
                    class="modulo-card"
                    data-modulo="5">

                    <span class="modulo-badge">
                        Bônus
                    </span>


                    <div class="modulo-icon">

                        <i class="fas fa-gem"></i>

                    </div>


                    <h3>
                        Módulo 5
                    </h3>


                    <p class="modulo-desc">
                        Cases e Simulações
                    </p>


                    <p class="modulo-lorem">
                        Lorem ipsum dolor sit amet,
                        consectetur adipiscing elit.
                        Vivamus lacinia odio.
                    </p>


                    <div class="modulo-aulas-count">

                        <i class="fas fa-play-circle"></i>

                        3 aulas

                    </div>


                    <button
                        type="button"
                        class="btn-entrar"
                        data-modulo="5">

                        Entrar

                        <i class="fas fa-arrow-right"></i>

                    </button>

                </div>


            </div>


            <button
                type="button"
                class="carousel-btn prev"
                id="prevBtn">

                <i class="fas fa-chevron-left"></i>

            </button>


            <button
                type="button"
                class="carousel-btn next"
                id="nextBtn">

                <i class="fas fa-chevron-right"></i>

            </button>


        </div>

    </section>


    <!-- ============================================================
         AULAS
    ============================================================ -->

    <section
        class="timeline-container"
        id="timelineContainer">


        <div class="timeline-header">


            <h2>

                <i class="fas fa-list-ul"></i>

                <span id="moduloTitulo">
                    Módulo 1
                </span>

            </h2>


            <button
                type="button"
                class="btn-voltar-modulos"
                id="btnVoltarModulos">

                <i class="fas fa-arrow-left"></i>

                Voltar aos módulos

            </button>


        </div>


        <div
            class="aulas-lista"
            id="aulasLista">
        </div>


    </section>


</main>


<!-- ================================================================
     MODAL DO VÍDEO
================================================================ -->

<div
    class="video-modal"
    id="videoModal">


    <div class="video-box">


        <div class="video-header">


            <h3 id="videoTitulo">
                Videoaula
            </h3>


            <button
                type="button"
                class="fechar-video"
                id="fecharVideo">

                <i class="fas fa-times"></i>

            </button>


        </div>


        <div
            class="video-area"
            id="videoArea">

            <div class="video-placeholder">

                <i class="fas fa-circle-play"></i>

                <h3>
                    Vídeo da aula
                </h3>

                <p>
                    O vídeo desta aula ainda não foi cadastrado.
                </p>

            </div>

        </div>


    </div>


</div>


<script>


/* ================================================================
   DADOS DAS AULAS
================================================================ */

const aulasPorModulo = {


    1: [

        {
            titulo:
                "Introdução ao Mundo dos Investimentos",

            professor:
                "Amanda Xagas",

            data:
                "10/03/2025",

            duracao:
                "12:30",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Tipos de Ativos Financeiros",

            professor:
                "Amanda Xagas",

            data:
                "12/03/2025",

            duracao:
                "18:45",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Risco e Retorno",

            professor:
                "Amanda Xagas",

            data:
                "14/03/2025",

            duracao:
                "22:10",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Montando sua Carteira Inicial",

            professor:
                "Amanda Xagas",

            data:
                "17/03/2025",

            duracao:
                "15:20",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        }

    ],


    2: [

        {
            titulo:
                "Gráficos e Tendências",

            professor:
                "Amanda Xagas",

            data:
                "20/03/2025",

            duracao:
                "14:50",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Indicadores Técnicos",

            professor:
                "Amanda Xagas",

            data:
                "22/03/2025",

            duracao:
                "20:10",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Suporte e Resistência",

            professor:
                "Amanda Xagas",

            data:
                "25/03/2025",

            duracao:
                "17:30",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Padrões de Candlestick",

            professor:
                "Amanda Xagas",

            data:
                "28/03/2025",

            duracao:
                "25:00",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        }

    ],


    3: [

        {
            titulo:
                "O que é Valuation?",

            professor:
                "Amanda Xagas",

            data:
                "01/04/2025",

            duracao:
                "16:40",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Fluxo de Caixa Descontado",

            professor:
                "Amanda Xagas",

            data:
                "03/04/2025",

            duracao:
                "22:30",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Múltiplos de Mercado",

            professor:
                "Amanda Xagas",

            data:
                "06/04/2025",

            duracao:
                "19:15",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Análise de Empresas",

            professor:
                "Amanda Xagas",

            data:
                "09/04/2025",

            duracao:
                "21:00",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        }

    ],


    4: [

        {
            titulo:
                "Derivativos e Opções",

            professor:
                "Amanda Xagas",

            data:
                "12/04/2025",

            duracao:
                "18:20",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Estratégias de Hedge",

            professor:
                "Amanda Xagas",

            data:
                "14/04/2025",

            duracao:
                "23:10",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Análise Macro e Micro",

            professor:
                "Amanda Xagas",

            data:
                "17/04/2025",

            duracao:
                "20:45",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Planejamento de Longo Prazo",

            professor:
                "Amanda Xagas",

            data:
                "20/04/2025",

            duracao:
                "16:30",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        }

    ],


    5: [

        {
            titulo:
                "Estudo de Caso: Small Caps",

            professor:
                "Amanda Xagas",

            data:
                "22/04/2025",

            duracao:
                "14:20",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Simulação de Carteira",

            professor:
                "Amanda Xagas",

            data:
                "24/04/2025",

            duracao:
                "19:40",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        },


        {
            titulo:
                "Análise de Risco Avançada",

            professor:
                "Amanda Xagas",

            data:
                "26/04/2025",

            duracao:
                "22:10",

            resumo:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
        }

    ]

};


/* ================================================================
   CARROSSEL
================================================================ */

const track =
    document.getElementById("modulosTrack");

const prevBtn =
    document.getElementById("prevBtn");

const nextBtn =
    document.getElementById("nextBtn");

let currentIndex = 0;


function getCardWidth() {

    const card =
        track.querySelector(".modulo-card");

    if (!card)
        return 0;

    return card.offsetWidth + 22;

}


function updateCarousel() {

    const cards =
        track.querySelectorAll(".modulo-card");

    if (!cards.length)
        return;

    const cardWidth =
        getCardWidth();

    const wrapper =
        track.parentElement.offsetWidth;

    const totalWidth =
        cards.length * cardWidth - 22;

    const maxOffset =
        Math.max(
            0,
            totalWidth - wrapper
        );

    let offset =
        currentIndex * cardWidth;

    offset =
        Math.min(
            offset,
            maxOffset
        );

    track.style.transform =
        `translateX(-${offset}px)`;

}


prevBtn.addEventListener(
    "click",
    function() {

        if (currentIndex > 0) {

            currentIndex--;

            updateCarousel();

        }

    }
);


nextBtn.addEventListener(
    "click",
    function() {

        const cards =
            track.querySelectorAll(
                ".modulo-card"
            );

        if (
            currentIndex <
            cards.length - 1
        ) {

            currentIndex++;

            updateCarousel();

        }

    }
);


window.addEventListener(
    "resize",
    updateCarousel
);


/* ================================================================
   ELEMENTOS DOS MÓDULOS
================================================================ */

const modulosSection =
    document.getElementById(
        "modulosSection"
    );

const timelineContainer =
    document.getElementById(
        "timelineContainer"
    );

const aulasLista =
    document.getElementById(
        "aulasLista"
    );

const moduloTitulo =
    document.getElementById(
        "moduloTitulo"
    );


/* ================================================================
   ABRIR MÓDULO
================================================================ */

function abrirModulo(moduloId) {


    const card =
        document.querySelector(
            `.modulo-card[data-modulo="${moduloId}"]`
        );


    if (!card)
        return;


    const nome =
        card.querySelector("h3").textContent;


    moduloTitulo.textContent =
        nome;


    const aulas =
        aulasPorModulo[moduloId] || [];


    aulasLista.innerHTML =
        "";


    aulas.forEach(
        function(aula, index) {


            const item =
                document.createElement("div");


            item.className =
                "aula-item";


            item.innerHTML = `

                <div class="aula-info">

                    <span class="aula-titulo">

                        ${aula.titulo}

                    </span>


                    <div class="aula-meta">

                        <span>

                            <i class="fas fa-user"></i>

                            ${aula.professor}

                        </span>


                        <span>

                            <i class="fas fa-calendar"></i>

                            ${aula.data}

                        </span>

                    </div>

                </div>


                <div class="aula-duracao">

                    <i class="fas fa-clock"></i>

                    ${aula.duracao}

                </div>


                <button
                    type="button"
                    class="btn-assistir"
                    data-aula="${index}"
                    data-modulo="${moduloId}">

                    <i class="fas fa-play"></i>

                    Assistir

                </button>


                <div class="tooltip">

                    <div class="tt-title">

                        ${aula.titulo}

                    </div>


                    <div class="tt-meta">

                        <span>

                            <i class="fas fa-user"></i>

                            Professor:
                            <strong>
                                ${aula.professor}
                            </strong>

                        </span>


                        <span>

                            <i class="fas fa-calendar"></i>

                            ${aula.data}

                        </span>


                        <span>

                            <i class="fas fa-clock"></i>

                            ${aula.duracao}

                        </span>

                    </div>


                    <div class="tt-resumo">

                        ${aula.resumo}

                    </div>

                </div>

            `;


            aulasLista.appendChild(item);

        }
    );


    modulosSection.style.display =
        "none";


    timelineContainer.classList.add(
        "active"
    );


    window.scrollTo({

        top:
            timelineContainer.offsetTop - 30,

        behavior:
            "smooth"

    });


}


/* ================================================================
   BOTÕES ENTRAR
================================================================ */

document
    .querySelectorAll(".btn-entrar")
    .forEach(
        function(button) {

            button.addEventListener(
                "click",
                function() {

                    const modulo =
                        Number(
                            this.dataset.modulo
                        );

                    abrirModulo(modulo);

                }
            );

        }
    );


/* ================================================================
   BOTÃO VOLTAR
================================================================ */

document
    .getElementById("btnVoltarModulos")
    .addEventListener(
        "click",
        function() {

            timelineContainer
                .classList
                .remove("active");


            modulosSection.style.display =
                "block";


            window.scrollTo({

                top: 0,

                behavior:
                    "smooth"

            });

        }
    );


/* ================================================================
   MODAL DO VÍDEO
================================================================ */

const videoModal =
    document.getElementById(
        "videoModal"
    );

const videoTitulo =
    document.getElementById(
        "videoTitulo"
    );

const videoArea =
    document.getElementById(
        "videoArea"
    );

const fecharVideo =
    document.getElementById(
        "fecharVideo"
    );


/*
    COLOQUE AQUI OS CAMINHOS DOS SEUS VÍDEOS.

    Exemplo:

    1: [
        "videos/modulo1/aula1.mp4",
        "videos/modulo1/aula2.mp4",
        "videos/modulo1/aula3.mp4",
        "videos/modulo1/aula4.mp4"
    ]

    Se deixar vazio, aparecerá a mensagem
    informando que o vídeo ainda não foi cadastrado.
*/

const videos = {

    1: [
        "",
        "",
        "",
        ""
    ],

    2: [
        "",
        "",
        "",
        ""
    ],

    3: [
        "",
        "",
        "",
        ""
    ],

    4: [
        "",
        "",
        "",
        ""
    ],

    5: [
        "",
        "",
        ""
    ]

};


/* ================================================================
   ABRIR VÍDEO
================================================================ */

document.addEventListener(
    "click",
    function(event) {


        const botao =
            event.target.closest(
                ".btn-assistir"
            );


        if (!botao)
            return;


        const modulo =
            Number(
                botao.dataset.modulo
            );


        const aula =
            Number(
                botao.dataset.aula
            );


        const dados =
            aulasPorModulo[modulo][aula];


        const caminho =
            videos[modulo]?.[aula] || "";


        videoTitulo.textContent =
            dados.titulo;


        if (caminho !== "") {

            videoArea.innerHTML = `

                <video
                    controls
                    autoplay>

                    <source
                        src="${caminho}"
                        type="video/mp4">

                    Seu navegador não suporta
                    reprodução de vídeo.

                </video>

            `;

        } else {

            videoArea.innerHTML = `

                <div class="video-placeholder">

                    <i class="fas fa-circle-play"></i>

                    <h3>
                        ${dados.titulo}
                    </h3>

                    <p>
                        O vídeo desta aula ainda
                        não foi cadastrado.
                    </p>

                </div>

            `;

        }


        videoModal.classList.add(
            "active"
        );


    }
);


/* ================================================================
   FECHAR VÍDEO
================================================================ */

function fecharModalVideo() {

    videoModal.classList.remove(
        "active"
    );


    const video =
        videoArea.querySelector(
            "video"
        );


    if (video) {

        video.pause();

        video.currentTime = 0;

    }

}


fecharVideo.addEventListener(
    "click",
    fecharModalVideo
);


videoModal.addEventListener(
    "click",
    function(event) {

        if (
            event.target ===
            videoModal
        ) {

            fecharModalVideo();

        }

    }
);


document.addEventListener(
    "keydown",
    function(event) {

        if (
            event.key === "Escape"
        ) {

            fecharModalVideo();

        }

    }
);


/* ================================================================
   INICIALIZAÇÃO
================================================================ */

window.addEventListener(
    "load",
    function() {

        updateCarousel();

    }
);

</script>


</body>

</html>
