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
    <title>Calendário Financeiro</title>
    <link rel="stylesheet" href="css/calendario.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include_once 'navbar.php'; ?>

<main>
    <!-- HEADER DO CALENDÁRIO -->
    <section class="calendario-header">
        <div class="calendario-header-content">
            <div class="header-left">
                <h1>
                    <i class="fas fa-calendar-alt" style="color: #16E28A;"></i> 
                    Calendário Financeiro
                </h1>
                <p>Visualize seus gastos e receitas mês a mês</p>
            </div>
            <div class="header-right">
                <button class="btn-filtrar" onclick="toggleFiltros()">
                    <i class="fas fa-sliders-h"></i> Filtrar
                </button>
            </div>
        </div>
    </section>

    <!-- FILTROS (colapsável) -->
    <section class="filtros-section" id="filtrosSection">
        <div class="filtros-container">
            <div class="filtro-item">
                <label><i class="fas fa-calendar"></i> Ano</label>
                <select>
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>
            </div>
            <div class="filtro-item">
                <label><i class="fas fa-tag"></i> Categoria</label>
                <select>
                    <option value="todas">Todas</option>
                    <option value="alimentacao">Alimentação</option>
                    <option value="transporte">Transporte</option>
                    <option value="moradia">Moradia</option>
                    <option value="lazer">Lazer</option>
                    <option value="saude">Saúde</option>
                </select>
            </div>
            <div class="filtro-item">
                <label><i class="fas fa-chart-pie"></i> Tipo</label>
                <select>
                    <option value="todos">Todos</option>
                    <option value="receita">Receitas</option>
                    <option value="despesa">Despesas</option>
                </select>
            </div>
            <button class="btn-aplicar-filtro">
                <i class="fas fa-check"></i> Aplicar
            </button>
        </div>
    </section>

    <!-- GRID DE MESES -->
    <section class="meses-grid">
        <!-- Mês 1: Janeiro -->
        <div class="mes-card" onclick="abrirDetalhesMes('Janeiro')">
            <div class="mes-header">
                <h3><i class="fas fa-calendar-check"></i> Janeiro</h3>
                <span class="mes-ano">2026</span>
            </div>
            <div class="mes-resumo">
                <div class="resumo-item receita">
                    <span class="rotulo">Receitas</span>
                    <span class="valor positivo">R$ 4.500,00</span>
                </div>
                <div class="resumo-item despesa">
                    <span class="rotulo">Despesas</span>
                    <span class="valor negativo">R$ 3.200,00</span>
                </div>
                <div class="resumo-item saldo">
                    <span class="rotulo">Saldo</span>
                    <span class="valor positivo">R$ 1.300,00</span>
                </div>
            </div>
            <div class="grafico-pizza-container">
                <canvas id="pizzaJan" width="120" height="120"></canvas>
            </div>
            <div class="mes-footer">
                <span class="categorias-count"><i class="fas fa-tags"></i> 5 categorias</span>
                <span class="transacoes-count"><i class="fas fa-exchange-alt"></i> 12 transações</span>
            </div>
        </div>

        <!-- Mês 2: Fevereiro -->
        <div class="mes-card" onclick="abrirDetalhesMes('Fevereiro')">
            <div class="mes-header">
                <h3><i class="fas fa-calendar-check"></i> Fevereiro</h3>
                <span class="mes-ano">2026</span>
            </div>
            <div class="mes-resumo">
                <div class="resumo-item receita">
                    <span class="rotulo">Receitas</span>
                    <span class="valor positivo">R$ 4.500,00</span>
                </div>
                <div class="resumo-item despesa">
                    <span class="rotulo">Despesas</span>
                    <span class="valor negativo">R$ 3.800,00</span>
                </div>
                <div class="resumo-item saldo">
                    <span class="rotulo">Saldo</span>
                    <span class="valor positivo">R$ 700,00</span>
                </div>
            </div>
            <div class="grafico-pizza-container">
                <canvas id="pizzaFev" width="120" height="120"></canvas>
            </div>
            <div class="mes-footer">
                <span class="categorias-count"><i class="fas fa-tags"></i> 4 categorias</span>
                <span class="transacoes-count"><i class="fas fa-exchange-alt"></i> 8 transações</span>
            </div>
        </div>

        <!-- Mês 3: Março -->
        <div class="mes-card" onclick="abrirDetalhesMes('Março')">
            <div class="mes-header">
                <h3><i class="fas fa-calendar-check"></i> Março</h3>
                <span class="mes-ano">2026</span>
            </div>
            <div class="mes-resumo">
                <div class="resumo-item receita">
                    <span class="rotulo">Receitas</span>
                    <span class="valor positivo">R$ 4.500,00</span>
                </div>
                <div class="resumo-item despesa">
                    <span class="rotulo">Despesas</span>
                    <span class="valor negativo">R$ 2.900,00</span>
                </div>
                <div class="resumo-item saldo">
                    <span class="valor positivo">R$ 1.600,00</span>
                </div>
            </div>
            <div class="grafico-pizza-container">
                <canvas id="pizzaMar" width="120" height="120"></canvas>
            </div>
            <div class="mes-footer">
                <span class="categorias-count"><i class="fas fa-tags"></i> 6 categorias</span>
                <span class="transacoes-count"><i class="fas fa-exchange-alt"></i> 15 transações</span>
            </div>
        </div>

        <!-- Mês 4: Abril -->
        <div class="mes-card" onclick="abrirDetalhesMes('Abril')">
            <div class="mes-header">
                <h3><i class="fas fa-calendar-check"></i> Abril</h3>
                <span class="mes-ano">2026</span>
            </div>
            <div class="mes-resumo">
                <div class="resumo-item receita">
                    <span class="rotulo">Receitas</span>
                    <span class="valor positivo">R$ 4.500,00</span>
                </div>
                <div class="resumo-item despesa">
                    <span class="rotulo">Despesas</span>
                    <span class="valor negativo">R$ 4.100,00</span>
                </div>
                <div class="resumo-item saldo">
                    <span class="rotulo">Saldo</span>
                    <span class="valor positivo">R$ 400,00</span>
                </div>
            </div>
            <div class="grafico-pizza-container">
                <canvas id="pizzaAbr" width="120" height="120"></canvas>
            </div>
            <div class="mes-footer">
                <span class="categorias-count"><i class="fas fa-tags"></i> 4 categorias</span>
                <span class="transacoes-count"><i class="fas fa-exchange-alt"></i> 10 transações</span>
            </div>
        </div>

        <!-- Mês 5: Maio -->
        <div class="mes-card" onclick="abrirDetalhesMes('Maio')">
            <div class="mes-header">
                <h3><i class="fas fa-calendar-check"></i> Maio</h3>
                <span class="mes-ano">2026</span>
            </div>
            <div class="mes-resumo">
                <div class="resumo-item receita">
                    <span class="rotulo">Receitas</span>
                    <span class="valor positivo">R$ 4.500,00</span>
                </div>
                <div class="resumo-item despesa">
                    <span class="rotulo">Despesas</span>
                    <span class="valor negativo">R$ 3.500,00</span>
                </div>
                <div class="resumo-item saldo">
                    <span class="rotulo">Saldo</span>
                    <span class="valor positivo">R$ 1.000,00</span>
                </div>
            </div>
            <div class="grafico-pizza-container">
                <canvas id="pizzaMai" width="120" height="120"></canvas>
            </div>
            <div class="mes-footer">
                <span class="categorias-count"><i class="fas fa-tags"></i> 5 categorias</span>
                <span class="transacoes-count"><i class="fas fa-exchange-alt"></i> 14 transações</span>
            </div>
        </div>

        <!-- Mês 6: Junho -->
        <div class="mes-card" onclick="abrirDetalhesMes('Junho')">
            <div class="mes-header">
                <h3><i class="fas fa-calendar-check"></i> Junho</h3>
                <span class="mes-ano">2026</span>
            </div>
            <div class="mes-resumo">
                <div class="resumo-item receita">
                    <span class="rotulo">Receitas</span>
                    <span class="valor positivo">R$ 4.500,00</span>
                </div>
                <div class="resumo-item despesa">
                    <span class="rotulo">Despesas</span>
                    <span class="valor negativo">R$ 2.700,00</span>
                </div>
                <div class="resumo-item saldo">
                    <span class="rotulo">Saldo</span>
                    <span class="valor positivo">R$ 1.800,00</span>
                </div>
            </div>
            <div class="grafico-pizza-container">
                <canvas id="pizzaJun" width="120" height="120"></canvas>
            </div>
            <div class="mes-footer">
                <span class="categorias-count"><i class="fas fa-tags"></i> 7 categorias</span>
                <span class="transacoes-count"><i class="fas fa-exchange-alt"></i> 18 transações</span>
            </div>
        </div>
    </section>

    <!-- MODAL DE DETALHES DO MÊS -->
    <div class="modal-overlay" id="modalDetalhes">
        <div class="modal-detalhes">
            <button class="modal-fechar" onclick="fecharDetalhes()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <h2 id="modalTituloMes"><i class="fas fa-calendar-alt"></i> Janeiro 2026</h2>
                <div class="modal-resumo-geral">
                    <div class="modal-resumo-item">
                        <span>Receitas</span>
                        <strong class="positivo">R$ 4.500,00</strong>
                    </div>
                    <div class="modal-resumo-item">
                        <span>Despesas</span>
                        <strong class="negativo">R$ 3.200,00</strong>
                    </div>
                    <div class="modal-resumo-item">
                        <span>Saldo</span>
                        <strong class="positivo">R$ 1.300,00</strong>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-grafico">
                    <h4>Distribuição de Gastos</h4>
                    <div class="grafico-grande-container">
                        <canvas id="modalPizzaGrande" width="300" height="300"></canvas>
                    </div>
                </div>
                <div class="modal-lista-transacoes">
                    <h4>Últimas Transações</h4>
                    <div class="transacao-item">
                        <span class="transacao-categoria"><i class="fas fa-utensils"></i> Alimentação</span>
                        <span class="transacao-descricao">Supermercado</span>
                        <span class="transacao-valor negativo">-R$ 350,00</span>
                    </div>
                    <div class="transacao-item">
                        <span class="transacao-categoria"><i class="fas fa-bus"></i> Transporte</span>
                        <span class="transacao-descricao">Uber</span>
                        <span class="transacao-valor negativo">-R$ 45,00</span>
                    </div>
                    <div class="transacao-item">
                        <span class="transacao-categoria"><i class="fas fa-home"></i> Moradia</span>
                        <span class="transacao-descricao">Aluguel</span>
                        <span class="transacao-valor negativo">-R$ 1.200,00</span>
                    </div>
                    <div class="transacao-item">
                        <span class="transacao-categoria"><i class="fas fa-film"></i> Lazer</span>
                        <span class="transacao-descricao">Cinema</span>
                        <span class="transacao-valor negativo">-R$ 60,00</span>
                    </div>
                    <div class="transacao-item">
                        <span class="transacao-categoria"><i class="fas fa-heartbeat"></i> Saúde</span>
                        <span class="transacao-descricao">Farmácia</span>
                        <span class="transacao-valor negativo">-R$ 120,00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    // ========== FUNÇÕES DO CALENDÁRIO ==========

    // Toggle dos filtros
    function toggleFiltros() {
        const section = document.getElementById('filtrosSection');
        section.classList.toggle('active');
    }

    // Abrir modal de detalhes
    function abrirDetalhesMes(mes) {
        document.getElementById('modalTituloMes').innerHTML = `<i class="fas fa-calendar-alt"></i> ${mes} 2026`;
        document.getElementById('modalDetalhes').classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Inicializa o gráfico grande
        criarGraficoGrande();
    }

    // Fechar modal de detalhes
    function fecharDetalhes() {
        document.getElementById('modalDetalhes').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Fechar modal com clique fora
    document.getElementById('modalDetalhes').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharDetalhes();
        }
    });

    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharDetalhes();
        }
    });

    // ========== CRIAÇÃO DOS GRÁFICOS ==========

    // Cores para os gráficos
    const cores = [
        '#16E28A', '#FF6B6B', '#4ECDC4', '#FFD93D', '#6C5CE7', 
        '#A8E6CF', '#FF8A5C', '#74B9FF', '#FD79A8', '#00B894'
    ];

    // Dados dos meses (exemplo)
    const dadosMeses = {
        'Janeiro': {
            categorias: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Saúde'],
            valores: [350, 200, 1200, 400, 150]
        },
        'Fevereiro': {
            categorias: ['Alimentação', 'Moradia', 'Lazer', 'Saúde'],
            valores: [400, 1200, 300, 180]
        },
        'Março': {
            categorias: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Saúde', 'Educação'],
            valores: [380, 250, 1200, 350, 200, 150]
        },
        'Abril': {
            categorias: ['Alimentação', 'Moradia', 'Lazer', 'Saúde'],
            valores: [420, 1200, 280, 200]
        },
        'Maio': {
            categorias: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Saúde'],
            valores: [360, 220, 1200, 380, 170]
        },
        'Junho': {
            categorias: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Saúde', 'Educação', 'Outros'],
            valores: [340, 200, 1200, 320, 180, 150, 100]
        }
    };

    // Criar gráficos pequenos para cada mês
    function criarGraficosPequenos() {
        const mesesIds = {
            'Janeiro': 'pizzaJan',
            'Fevereiro': 'pizzaFev',
            'Março': 'pizzaMar',
            'Abril': 'pizzaAbr',
            'Maio': 'pizzaMai',
            'Junho': 'pizzaJun'
        };

        Object.keys(mesesIds).forEach(mes => {
            const canvasId = mesesIds[mes];
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const ctx = canvas.getContext('2d');
                const dados = dadosMeses[mes];
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: dados.categorias,
                        datasets: [{
                            data: dados.valores,
                            backgroundColor: cores.slice(0, dados.categorias.length),
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percent = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: R$ ${context.parsed} (${percent}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        });
    }

    // Criar gráfico grande no modal
    let graficoGrande = null;

    function criarGraficoGrande() {
        const canvas = document.getElementById('modalPizzaGrande');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const dados = dadosMeses['Janeiro'];
        
        // Destroi gráfico anterior se existir
        if (graficoGrande) {
            graficoGrande.destroy();
        }
        
        graficoGrande = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: dados.categorias,
                datasets: [{
                    data: dados.valores,
                    backgroundColor: cores.slice(0, dados.categorias.length),
                    borderColor: 'rgba(10, 37, 64, 0.8)',
                    borderWidth: 2,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#ffffff',
                            font: { size: 12 },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percent = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: R$ ${context.parsed} (${percent}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                animation: {
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }

    // Inicializar gráficos quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        criarGraficosPequenos();
    });
</script>

</body>
</html>