<?php
session_start();
require_once("../back-end/conexao.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../front-end/login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

$sql = "
SELECT
SUM(CASE WHEN tipo='entrada' THEN valor ELSE 0 END)
-
SUM(CASE WHEN tipo='saida' THEN valor ELSE 0 END)
AS saldo
FROM movimentacoes
WHERE usuario_id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);

$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$saldo = $resultado['saldo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Financeiro</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/carteira.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>
<body>

<?php include_once'navbar.php'; ?>

<div class="container">

    <!-- Main -->
    <main class="main">

        <!-- Cards -->
        <section class="cards">
            <div class="card">
                <h4>Saldo Atual</h4>
                <p class="green">
                    R$ <?php echo number_format($saldo, 2, ',', '.'); ?>
                </p>
            </div>

            <div class="card">
                <h4>Receita do mês</h4>
                <p>+ R$ 2.005</p>
            </div>

            <div class="card">
                <h4>Despesas do mês</h4>
                <p class="red">- R$ 1.590</p>
            </div>

            <div class="card">
                <h4>Meta do mês</h4>
                <p class="green">75% Atingida</p>
                <div class="progress">
                    <div class="bar progresso-75"></div>
                </div>
            </div>
        </section>

        <section class="controle-financeiro">
            <div class="section-header">
                <div>
                    <h2>💰 Controle Financeiro</h2>
                    <p>Cadastre receitas e despesas para acompanhar suas finanças.</p>
                </div>

                <button type="button" class="btn-movimentacao" onclick="abrirModal()">
                    ➕ Nova Movimentação
                </button>
                <button type="button" class="btn-movimentacao" id="pluggyConnectButton">
                    Conectar Conta Bancária
                </button>
            </div>
        </section>

        <!-- ================= OPEN FINANCE ================= -->

        <section class="controle-financeiro">
            <div class="section-header">
                <div>
                    <h2>📈 Investimentos</h2>
                    <p>Dados vindos da sua conta conectada via Open Finance.</p>
                </div>
            </div>

            <div id="investimentosContainer" class="of-lista">
                <p class="of-status">Carregando investimentos...</p>
            </div>
        </section>

        <section class="controle-financeiro">
            <div class="section-header">
                <div>
                    <h2>🔁 Transações</h2>
                    <p>Últimas transações da sua conta conectada.</p>
                </div>
            </div>

            <div id="transacoesContainer" class="of-lista">
                <p class="of-status">Carregando transações...</p>
            </div>
        </section>

        <section class="controle-financeiro">
            <div class="section-header">
                <div>
                    <h2>🏦 Empréstimos</h2>
                    <p>Empréstimos identificados na sua conta conectada.</p>
                </div>
            </div>

            <div id="emprestimosContainer" class="of-lista">
                <p class="of-status">Carregando empréstimos...</p>
            </div>
        </section>

    </main>
</div>

<div id="modalMovimentacao" class="modal">

    <div class="modal-content">

        <div class="modal-header">
            <h2>Nova Movimentação</h2>

            <button type="button" class="close-modal" onclick="fecharModal()">
                ✕
            </button>
        </div>

        <form action="../back-end/processa_movimentacao.php" method="POST">
            <div class="form-group">
                <label>Valor</label>
                <input
                    type="number"
                    step="0.01"
                    name="valor"
                    required
                >
            </div>

            <div class="form-group">
                <label>Tipo</label>

                <select name="tipo" id="tipoSelect" required>
                    <option value="" selected disabled>Selecione</option>
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                </select>
            </div>

            <div class="form-group">
                <label>Categoria</label>

                <select name="categoria" id="categoriaSelect" required disabled>
                    <option value="" selected disabled>Selecione o tipo primeiro</option>
                </select>
            </div>

            <div class="form-group">
                <label>Descrição</label>
                <input
                    type="text"
                    name="descricao"
                    placeholder="Ex: Compra do mês"
                    required
                >
            </div>


            <button type="submit" class="btn-salvar">
                Salvar Movimentação
            </button>

        </form>

    </div>

</div>

<script>
/* ============================================================
   Modal de Movimentação + Categorias Dinâmicas
   (inline para descartar problema de caminho do arquivo js/)
   ============================================================ */

const CATEGORIAS_POR_TIPO = {
    entrada: [
        { valor: "Salário",       label: " Salário" },
        { valor: "Freelance",     label: " Freelance" },
        { valor: "Investimentos", label: " Investimentos" },
        { valor: "Presente",      label: " Presente" },
        { valor: "Outros",        label: " Outros" }
    ],
    saida: [
        { valor: "Alimentação", label: " Alimentação" },
        { valor: "Transporte",  label: " Transporte" },
        { valor: "Moradia",     label: " Moradia" },
        { valor: "Saúde",       label: " Saúde" },
        { valor: "Educação",    label: " Educação" },
        { valor: "Lazer",       label: " Lazer" },
        { valor: "Outros",      label: " Outros" }
    ]
};

const modal = document.getElementById("modalMovimentacao");
const selectTipo = document.getElementById("tipoSelect");
const selectCategoria = document.getElementById("categoriaSelect");

if (!modal) {
    console.error("ERRO: elemento #modalMovimentacao não encontrado no HTML.");
}

function abrirModal() {
    console.log("abrirModal() chamado"); // debug temporário
    if (!modal) return;
    modal.classList.add("active");
    document.body.style.overflow = "hidden";
}

function fecharModal() {
    if (!modal) return;
    modal.classList.remove("active");
    document.body.style.overflow = "";

    const form = modal.querySelector("form");
    if (form) form.reset();
    if (selectCategoria) {
        selectCategoria.innerHTML = '<option value="" selected disabled>Selecione o tipo primeiro</option>';
        selectCategoria.disabled = true;
    }
}

function atualizarCategorias() {
    const tipo = selectTipo.value;
    selectCategoria.innerHTML = '<option value="" selected disabled>Selecione uma categoria</option>';

    if (!tipo || !CATEGORIAS_POR_TIPO[tipo]) {
        selectCategoria.disabled = true;
        return;
    }

    selectCategoria.disabled = false;

    CATEGORIAS_POR_TIPO[tipo].forEach(cat => {
        const option = document.createElement("option");
        option.value = cat.valor;
        option.textContent = cat.label;
        selectCategoria.appendChild(option);
    });
}

if (selectTipo) {
    selectTipo.addEventListener("change", atualizarCategorias);
}

if (modal) {
    modal.addEventListener("click", (e) => {
        if (e.target === modal) fecharModal();
    });
}

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal && modal.classList.contains("active")) {
        fecharModal();
    }
});

/* ============================================================
   Open Finance (Pluggy) - Conexão + exibição dos dados
   ============================================================ */

// -------- Helpers de formatação --------
function formatarMoeda(valor) {
    const numero = Number(valor) || 0;
    return numero.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
}

function formatarData(dataStr) {
    if (!dataStr) return '-';
    const data = new Date(dataStr);
    if (isNaN(data.getTime())) return dataStr;
    return data.toLocaleDateString('pt-br');
}

// -------- Carrega Investimentos --------
async function carregarInvestimentos() {
    const container = document.getElementById("investimentosContainer");

    try {
        const resposta = await fetch("../open-finance/investimentos.php");
        const dados = await resposta.json();

        if (dados.error) {
            container.innerHTML = `<p class="of-status">${dados.error}</p>`;
            return;
        }

        const investimentos = dados.investimentos || [];

        if (investimentos.length === 0) {
            container.innerHTML = `<p class="of-status">Nenhum investimento encontrado.</p>`;
            return;
        }

        container.innerHTML = investimentos.map(inv => `
            <div class="of-item">
                <div class="of-item-topo">
                    <strong>${inv.name ?? 'Investimento'}</strong>
                    <span class="of-tag">${inv.type ?? ''}</span>
                </div>
                <div class="of-item-linha">
                    <span>Saldo</span>
                    <span>${formatarMoeda(inv.balance)}</span>
                </div>
                <div class="of-item-linha">
                    <span>Valor investido</span>
                    <span>${formatarMoeda(inv.amount)}</span>
                </div>
            </div>
        `).join("");

    } catch (erro) {
        console.error("Erro ao carregar investimentos:", erro);
        container.innerHTML = `<p class="of-status">Erro ao carregar investimentos.</p>`;
    }
}

// -------- Carrega Transações --------
async function carregarTransacoes() {
    const container = document.getElementById("transacoesContainer");

    try {
        const resposta = await fetch("../open-finance/transacoes.php");
        const dados = await resposta.json();

        if (dados.error) {
            container.innerHTML = `<p class="of-status">${dados.error}</p>`;
            return;
        }

        const transacoes = dados.transacoes || [];

        if (transacoes.length === 0) {
            container.innerHTML = `<p class="of-status">Nenhuma transação encontrada.</p>`;
            return;
        }

        container.innerHTML = transacoes.map(t => {
            const positivo = Number(t.amount) >= 0;
            return `
                <div class="of-item">
                    <div class="of-item-topo">
                        <strong>${t.description ?? 'Transação'}</strong>
                        <span class="of-tag">${t.contaNome ?? ''}</span>
                    </div>
                    <div class="of-item-linha">
                        <span>${formatarData(t.date)}</span>
                        <span class="${positivo ? 'green' : 'red'}">
                            ${positivo ? '+' : ''}${formatarMoeda(t.amount)}
                        </span>
                    </div>
                </div>
            `;
        }).join("");

    } catch (erro) {
        console.error("Erro ao carregar transações:", erro);
        container.innerHTML = `<p class="of-status">Erro ao carregar transações.</p>`;
    }
}

// -------- Carrega Empréstimos --------
async function carregarEmprestimos() {
    const container = document.getElementById("emprestimosContainer");

    try {
        const resposta = await fetch("../open-finance/emprestimos.php");
        const dados = await resposta.json();

        if (dados.error) {
            container.innerHTML = `<p class="of-status">${dados.error}</p>`;
            return;
        }

        const emprestimos = dados.emprestimos || [];

        if (emprestimos.length === 0) {
            container.innerHTML = `<p class="of-status">Nenhum empréstimo encontrado.</p>`;
            return;
        }

        container.innerHTML = emprestimos.map(e => `
            <div class="of-item">
                <div class="of-item-topo">
                    <strong>${e.contractNumber ?? 'Empréstimo'}</strong>
                    <span class="of-tag">${e.type ?? ''}</span>
                </div>
                <div class="of-item-linha">
                    <span>Valor total</span>
                    <span>${formatarMoeda(e.contractAmount)}</span>
                </div>
                <div class="of-item-linha">
                    <span>Parcelas restantes</span>
                    <span>${e.numberOfInstallments ?? '-'}</span>
                </div>
            </div>
        `).join("");

    } catch (erro) {
        console.error("Erro ao carregar empréstimos:", erro);
        container.innerHTML = `<p class="of-status">Erro ao carregar empréstimos.</p>`;
    }
}

function carregarDadosOpenFinance() {
    carregarInvestimentos();
    carregarTransacoes();
    carregarEmprestimos();
}

// -------- Conexão com o Pluggy Connect --------
async function initPluggy() {
    try {
        // busca o connect-token no open-finance
        const response = await fetch("../open-finance/connect-token.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            // envia uma requisição para o connect-token levando o valor de clientUserId
            body: JSON.stringify({
                clientUserId: "bryan-001"
            })
        });

        const data = await response.json();

        // Valida se o token realmente veio na resposta
        const connectToken = data.accessToken;

        if (!response.ok || !connectToken) {
            throw new Error(data.error || "Falha ao obter o connectToken do servidor.");
        }

        if (typeof window.PluggyConnect !== 'function') {
            throw new Error("A biblioteca PluggyConnect não foi carregada no HTML.");
        }

        const pluggyConnect = new window.PluggyConnect({
            connectToken: connectToken,
            includeSandbox: true,

            onSuccess: async (itemData) => {
                console.log(itemData);

                const itemId = itemData.item.id;

                const resposta = await fetch("../open-finance/item.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        itemId: itemId
                    })
                });

                const resultado = await resposta.json();
                console.log(resultado);

                // Assim que o item é salvo, recarrega os dados na tela
                if (resultado.success) {
                    carregarDadosOpenFinance();
                }
            },

            onError: (error) => {
                console.error('Erro na conexão do Pluggy Connect:', error);
            }
        });

        pluggyConnect.init();

    } catch (error) {
        console.error("Erro na inicialização:", error.message);
    }
}

const pluggyConnectButton = document.getElementById("pluggyConnectButton");
pluggyConnectButton.addEventListener("click", () => {
    initPluggy();
});

// Ao carregar a página, já tenta exibir os dados de quem já conectou antes
document.addEventListener("DOMContentLoaded", carregarDadosOpenFinance);
</script>
<script src="https://cdn.pluggy.ai/pluggy-connect/latest/pluggy-connect.js"></script>

</body>
</html>