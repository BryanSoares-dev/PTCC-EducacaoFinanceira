<?php
session_start();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Diversificação de Investimentos</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/investimentos.css">
</head>
<body>

<?php include_once'navbar.php'; ?>

<main>

    <!-- HERO DIVERSIFICAÇÃO -->
    <section class="hero_fixa" style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);">
        <div class="hero_fixa_container">
            <div class="hero_fixa_content">
                <span class="tag_fixa" style="background: rgba(46, 204, 113, 0.12); color: #2ecc71; border-color: rgba(46, 204, 113, 0.3);">
                     Estratégia Inteligente
                </span>
                <h1>Diversificação<br><span class="destaque" style="background: linear-gradient(135deg, #2ecc71, #27ae60); background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Não Coloque Todos os Ovos na Mesma Cesta</span></h1>
                <p>Aprenda a distribuir seus investimentos de forma inteligente para reduzir riscos e maximizar oportunidades de ganho no longo prazo.</p>
                <div class="hero_fixa_buttons">
                    <button class="btn_fixa_primary" style="background: #2ecc71;" onclick="scrollToConteudo()">Aprender Estratégias</button>
                    <button class="btn_fixa_secondary" onclick="window.location.href='home.php#investimentos'">Voltar</button>
                </div>
            </div>
            <div class="hero_fixa_card">
                <div class="card_fixa_info">
                    <div class="taxa_info">
                        <span>Redução de Risco</span>
                        <h3>Até 70%</h3>
                        <small>com diversificação adequada</small>
                    </div>
                    <div class="taxa_info">
                        <span>Carteiras Diversificadas</span>
                        <h3>+40%</h3>
                        <small>retorno no longo prazo</small>
                    </div>
                    <div class="taxa_info">
                        <span>Investidores que Diversificam</span>
                        <h3>85%</h3>
                        <small>têm melhores resultados</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- O QUE É DIVERSIFICAÇÃO -->
    <section class="o_que_e">
        <div class="section_title">
            <h2>O que é <span class="destaque">Diversificação?</span></h2>
            <p>Entenda o conceito fundamental dos investimentos</p>
        </div>
        <div class="explicacao_container">
            <div class="explicacao_texto">
                <p><strong>Diversificação</strong> é a estratégia de distribuir seus investimentos entre diferentes tipos de ativos, setores e mercados para reduzir os riscos da sua carteira.</p>
                <p>É como o ditado popular: <strong>"não coloque todos os ovos na mesma cesta"</strong>. Se um investimento cair, outros podem subir, equilibrando seus resultados e protegendo seu patrimônio.</p>
                <div class="destaque_box" style="background: linear-gradient(135deg, rgba(46, 204, 113, 0.08), rgba(46, 204, 113, 0.03)); border-left: 4px solid #2ecc71;">
                    <span> Objetivo Principal</span>
                    <p>Maximizar retornos para um determinado nível de risco ou minimizar riscos para um determinado nível de retorno esperado.</p>
                </div>
            </div>
            <div class="explicacao_icon">
                <div class="icon_circle" style="background: linear-gradient(135deg, rgba(46, 204, 113, 0.15), rgba(46, 204, 113, 0.05)); border: 2px solid rgba(46, 204, 113, 0.3);">
                    
                </div>
            </div>
        </div>
    </section>

    <!-- COMO FUNCIONA A DIVERSIFICAÇÃO -->
    <section class="como_funciona_fixa">
        <div class="section_title">
            <h2>Como <span class="destaque">Funciona</span> na Prática</h2>
            <p>Os pilares da diversificação inteligente</p>
        </div>
        <div class="steps_fixa">
            <div class="step_fixa">
                <div class="step_num" style="background: #2ecc71;">1</div>
                <h3>Classes de Ativos</h3>
                <p>Distribua entre Renda Fixa, Renda Variável, Imóveis e Internacional</p>
            </div>
            <div class="step_fixa">
                <div class="step_num" style="background: #2ecc71;">2</div>
                <h3>Setores da Economia</h3>
                <p>Invista em diferentes setores: tecnologia, saúde, energia, financeiro</p>
            </div>
            <div class="step_fixa">
                <div class="step_num" style="background: #2ecc71;">3</div>
                <h3>Prazos Diferentes</h3>
                <p>Combine investimentos de curto, médio e longo prazo</p>
            </div>
            <div class="step_fixa">
                <div class="step_num" style="background: #2ecc71;">4</div>
                <h3>Rebalanceamento</h3>
                <p>Ajuste periodicamente para manter a proporção ideal</p>
            </div>
        </div>
    </section>

    <!-- TIPOS DE DIVERSIFICAÇÃO -->
    <section class="tipos_fixa">
        <div class="section_title">
            <h2>Tipos de <span class="destaque">Diversificação</span></h2>
            <p>Conheça as principais formas de distribuir seus investimentos</p>
        </div>
        <div class="tipos_grid_fixa">
            <div class="tipo_card_fixa">
                <div class="tipo_icon_fixa" style="background: rgba(46, 204, 113, 0.1);">
                    <span style="font-size: 40px;"></span>
                </div>
                <h3>Por Classes de Ativos</h3>
                <p class="tipo_desc_fixa">Distribuição entre diferentes tipos de investimentos com comportamentos distintos.</p>
                <div class="tipo_vantagens_fixa">
                    <span>Ações</span>
                    <span>Renda Fixa</span>
                    <span>Imóveis</span>
                    <span> Internacional</span>
                </div>
                <p class="tipo_rentabilidade_fixa"><strong>Benefício:</strong> Redução da volatilidade total da carteira</p>
            </div>

            <div class="tipo_card_fixa">
                <div class="tipo_icon_fixa" style="background: rgba(46, 204, 113, 0.1);">
                    <span style="font-size: 40px;"></span>
                </div>
                <h3>Por Setores</h3>
                <p class="tipo_desc_fixa">Invista em empresas de diferentes segmentos da economia.</p>
                <div class="tipo_vantagens_fixa">
                    <span> Tecnologia</span>
                    <span> Saúde</span>
                    <span> Energia</span>
                    <span> Financeiro</span>
                </div>
                <p class="tipo_rentabilidade_fixa"><strong>Benefício:</strong> Proteção contra crises setoriais específicas</p>
            </div>

            <div class="tipo_card_fixa">
                <div class="tipo_icon_fixa" style="background: rgba(46, 204, 113, 0.1);">
                    <span style="font-size: 40px;"></span>
                </div>
                <h3>Geográfica</h3>
                <p class="tipo_desc_fixa">Invista em diferentes países e regiões do mundo.</p>
                <div class="tipo_vantagens_fixa">
                    <span>🇧🇷 Brasil</span>
                    <span>🇺🇸 EUA</span>
                    <span>🇪🇺 Europa</span>
                    <span>🇨🇳 Ásia</span>
                </div>
                <p class="tipo_rentabilidade_fixa"><strong>Benefício:</strong> Proteção contra riscos políticos e econômicos locais</p>
            </div>

            <div class="tipo_card_fixa">
                <div class="tipo_icon_fixa" style="background: rgba(46, 204, 113, 0.1);">
                    <span style="font-size: 40px;"></span>
                </div>
                <h3>Por Prazos</h3>
                <p class="tipo_desc_fixa">Combine investimentos com diferentes vencimentos.</p>
                <div class="tipo_vantagens_fixa">
                    <span>Curto Prazo</span>
                    <span>Médio Prazo</span>
                    <span> Longo Prazo</span>
                </div>
                <p class="tipo_rentabilidade_fixa"><strong>Benefício:</strong> Liquidez imediata + rentabilidade de longo prazo</p>
            </div>
        </div>
    </section>

    <!-- VANTAGENS E DESVANTAGENS -->
    <section class="prós_contrass">
        <div class="section_title">
            <h2>Vantagens vs <span class="destaque">Desvantagens</span></h2>
            <p>Entenda os prós e contras da diversificação</p>
        </div>
        <div class="comparativo_grid">
            <div class="vantagens_box">
                <h3>✅ Vantagens</h3>
                <ul>
                    <li>Redução significativa do risco total</li>
                    <li>Proteção contra perdas catastróficas</li>
                    <li>Maior estabilidade nos retornos</li>
                    <li>Aproveita oportunidades em diferentes mercados</li>
                    <li>Melhor relação risco-retorno no longo prazo</li>
                    <li>Menos estresse com oscilações do mercado</li>
                </ul>
            </div>
            <div class="desvantagens_box">
                <h3>❌ Desvantagens</h3>
                <ul>
                    <li>Pode limitar ganhos extraordinários</li>
                    <li>Exige mais conhecimento e pesquisa</li>
                    <li>Custos de transação podem ser maiores</li>
                    <li>Complexidade na gestão da carteira</li>
                    <li>Risco de diversificação excessiva (overdiversification)</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- DICAS PRÁTICAS -->
    <section class="dicas_praticas" style="background: linear-gradient(135deg, #0f2027, #203a43);">
        <div class="section_title">
            <h2>Estratégias <span class="destaque">Práticas</span></h2>
            <p>Como aplicar a diversificação no seu dia a dia</p>
        </div>
        <div class="dicas_grid">
            <div class="dica_item">
                <div class="dica_num" style="color: #2ecc71;">01</div>
                <div class="dica_content">
                    <h3>Regra 60/40</h3>
                    <p>Clássica: 60% em Renda Variável e 40% em Renda Fixa. Ajuste conforme seu perfil de risco.</p>
                </div>
            </div>
            <div class="dica_item">
                <div class="dica_num" style="color: #2ecc71;">02</div>
                <div class="dica_content">
                    <h3>ETFs e Fundos de Índice</h3>
                    <p>Invista em ETFs que replicam índices como IBOVESPA, S&P 500 para diversificação instantânea.</p>
                </div>
            </div>
            <div class="dica_item">
                <div class="dica_num" style="color: #2ecc71;">03</div>
                <div class="dica_content">
                    <h3>Rebalanceamento Anual</h3>
                    <p>A cada ano, ajuste sua carteira para manter as proporções originais. Venda o que subiu muito, compre o que caiu.</p>
                </div>
            </div>
            <div class="dica_item">
                <div class="dica_num" style="color: #2ecc71;">04</div>
                <div class="dica_content">
                    <h3>Invista no Exterior</h3>
                    <p>Aloque 20-30% em ativos internacionais (BDRs, ETFs globais) para proteção cambial e acesso a mercados desenvolvidos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- EXEMPLO PRÁTICO -->
    <section class="exemplo_container">
        <div class="exemplo_box">
            <div class="exemplo_header">
                <span style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">💡 Exemplo Prático</span>
                <h2>Carteira Concentrada vs Diversificada  </h2>
            </div>
            <div class="exemplo_calc">
                <div class="exemplo_valor" style="border: 2px solid #e74c3c;">
                    <h4>❌ Carteira Concentrada</h4>
                    <p class="valor_medio" style="margin: 10px 0;">90% Ações BR</p>
                    <p class="valor_medio" style="margin: 10px 0;">10% Renda Fixa</p>
                    <p style="color: #e74c3c; margin-top: 15px;"><strong>Risco: Alto</strong></p>
                    <p style="color: #e74c3c;">Volatilidade: +25%</p>
                    <p style="color: #e74c3c;">Perda potencial: -40%</p>
                </div>
                <div class="exemplo_seta" style="color: #2ecc71;">VS</div>
                <div class="exemplo_valor" style="border: 2px solid #2ecc71;">
                    <h4>✅ Carteira Diversificada</h4>
                    <p class="valor_medio" style="margin: 10px 0;">30% Ações BR</p>
                    <p class="valor_medio" style="margin: 10px 0;">25% Internacional</p>
                    <p class="valor_medio" style="margin: 10px 0;">25% Renda Fixa</p>
                    <p class="valor_medio" style="margin: 10px 0;">20% FIIs</p>
                    <p style="color: #2ecc71; margin-top: 15px;"><strong>Risco: Moderado</strong></p>
                    <p style="color: #2ecc71;">Volatilidade: +12%</p>
                    <p style="color: #2ecc71;">Perda potencial: -15%</p>
                </div>
            </div>
            <p class="exemplo_nota">*Exemplo simplificado para ilustrar o efeito da diversificação na redução de risco</p>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta_fixa" style="background: linear-gradient(135deg, #0f2027, #203a43);">
        <div class="cta_fixa_content">
            <h2>Comece a diversificar seus investimentos hoje</h2>
            <p>Analise sua carteira atual e distribua seus recursos de forma inteligente</p>
            <div class="cta_fixa_buttons">
                <?php if (isset($_SESSION['id'])): ?>
                    <button class="btn_cta" style="background: #2ecc71;" onclick="window.location.href='../front-end/carteira.php'">Ir para minha Carteira</button>
                    <button class="btn_cta_sec" onclick="window.location.href='../front-end/analisador.php'">Analisar Diversificação</button>
                <?php else: ?>
                    <button class="btn_cta" style="background: #2ecc71;" onclick="window.location.href='cadastro.php'">Criar Conta Grátis</button>
                    <button class="btn_cta_sec" onclick="window.location.href='login.php'">Já tenho conta</button>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<script>
    function scrollToConteudo() {
        document.querySelector('.o_que_e').scrollIntoView({ behavior: 'smooth' });
    }
</script>

</body>
</html>