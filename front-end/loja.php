<?php
// Inclui a conexão e sessão
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Loja</title>
    <link rel="stylesheet" href="../css/app.css" />
    <link rel="icon" type="image/svg+xml" href="../img/favicon.svg" />
    
</head>
<body>

<?php include_once 'navbar.php'; ?>

<main>

    <!-- ========== HERO ========== -->
    <section class="hero-loja">
        <div class="hero-container">
            <div class="hero-texto">
                <h1>Loja <span class="destaque">Premium</span></h1>
                <p>Ganhe <strong>Afidis</strong> estudando e troque por itens especiais. Sem pay‑to‑win, só dedicação.</p>
            </div>

            <div class="saldo-card">
                <div class="moeda-simbolo">
                    <div class="moeda-icone">$</div>
                    <span class="moeda-nome">Afidis</span>
                </div>
                <div class="saldo-valor">
                    343 <small>Af</small>
                </div>
                <div class="saldo-extra">
                    <i class="fas fa-arrow-up"></i> +236 esta semana
                </div>
            </div>
        </div>
    </section>

    <!-- ========== COMO GANHAR ========== -->
    <div class="como-ganhar">
        <div class="como-ganhar-box">
            <i class="fas fa-coins"></i>
            <span>Como ganhar</span>
            <span>a cada 10 XP → 1 Afidis</span>
            <span>Treino do Dia</span>
            <span>Desafios</span>
            <span style="background:rgba(255,255,255,0.05);border-color:transparent;color:rgba(255,255,255,0.5);">
                <i class="fas fa-gem" style="color:#FFA502;"></i> + bônus
            </span>
        </div>
    </div>

    <!-- ========== GRID DE ITENS ========== -->
    <section class="loja-grid">

        <div class="item-card">
            <div class="item-icone"><i class="fas fa-snowflake"></i></div>
            <h3>Congelar Streak</h3>
            <p class="item-desc">Mantenha sua sequência de estudos ativa por um dia, mesmo sem estudar.</p>
            <div class="item-preco">
                <i class="fas fa-coins" style="color:#16E28A;font-size:0.9rem;"></i> 170 <span class="moeda-mini">Af</span>
            </div>
            <button class="btn-comprar" onclick="comprar('Congelar Streak', 170)">Comprar</button>
        </div>

        <div class="item-card">
            <div class="item-icone"><i class="fas fa-image"></i></div>
            <h3>Banner de Perfil</h3>
            <p class="item-desc">Personalize seu perfil com um banner exclusivo e destaque-se na plataforma.</p>
            <div class="item-preco">
                <i class="fas fa-coins" style="color:#16E28A;font-size:0.9rem;"></i> 590 <span class="moeda-mini">Af</span>
            </div>
            <button class="btn-comprar" onclick="comprar('Banner de Perfil', 590)">Comprar</button>
        </div>

        <div class="item-card">
            <div class="item-icone"><i class="fas fa-coins" style="color:#FFD700;"></i></div>
            <h3>Double Coins</h3>
            <p class="item-desc">Dobre os Afidis ganhos durante 24 horas — ideal para acelerar sua evolução.</p>
            <div class="item-preco">
                <i class="fas fa-coins" style="color:#16E28A;font-size:0.9rem;"></i> 450 <span class="moeda-mini">Af</span>
            </div>
            <button class="btn-comprar" onclick="comprar('Double Coins', 450)">Comprar</button>
        </div>

        <div class="item-card">
            <div class="item-icone"><i class="fas fa-star" style="color:#FFA502;"></i></div>
            <h3>Double XP</h3>
            <p class="item-desc">Dobre sua experiência (XP) durante um dia inteiro — suba de nível mais rápido.</p>
            <div class="item-preco">
                <i class="fas fa-coins" style="color:#16E28A;font-size:0.9rem;"></i> 450 <span class="moeda-mini">Af</span>
            </div>
            <button class="btn-comprar" onclick="comprar('Double XP', 450)">Comprar</button>
        </div>

    </section>

    <!-- ========== AVISO ========== -->
    <div class="aviso-loja">
        <i class="fas fa-shield-alt"></i> Sem pay‑to‑win. Afidis só compra cosméticos e utilidades — nada que dê vantagem no estudo. <strong>Dedicação e técnica vencem força.</strong>
    </div>

</main>

<script>
    function comprar(nome, preco) {
        const saldoAtual = 343;
        if (saldoAtual >= preco) {
            alert(`🛒 Compra realizada!\n\nItem: ${nome}\nPreço: ${preco} Afidis\nSaldo restante: ${saldoAtual - preco} Afidis`);
        } else {
            alert(`❌ Saldo insuficiente!\n\nVocê tem ${saldoAtual} Afidis e o item custa ${preco} Afidis.\nEstude mais para ganhar mais Afidis!`);
        }
    }
</script>

</body>
</html>
