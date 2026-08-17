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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financeiro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/carteira.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>
<body>

<?php include_once'navbar.php'; ?>

<div class="container">

    <!-- Main -->
    <main class="main">

        <section class="carteira-hero">
            <div>
                <span class="carteira-tag">Visão geral</span>
                <h1>Sua carteira financeira</h1>
                <p>Acompanhe seu saldo, organize movimentações e consulte os dados da sua conta conectada.</p>
            </div>
            <div class="hero-saldo">
                <span>Saldo disponível</span>
                <strong>R$ <?php echo number_format($saldo, 2, ',', '.'); ?></strong>
            </div>
        </section>

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

        <section class="dados-open-finance" aria-live="polite">
            <div class="open-finance-cabecalho">
                <div><h2>Dados da conta conectada</h2><p id="openFinanceStatus">Conecte uma conta bancária para visualizar os dados.</p></div>
                <button type="button" class="btn-atualizar" id="atualizarOpenFinance" hidden>Atualizar dados</button>
            </div>
            <div class="open-finance-grid">
                <article class="open-finance-bloco transacoes-bloco"><div class="titulo-open-finance"><h3>Transações</h3><span id="totalTransacoes">0</span></div><div id="listaTransacoes" class="lista-open-finance"></div></article>
                <article class="open-finance-bloco emprestimos-bloco"><div class="titulo-open-finance"><h3>Empréstimos</h3><span id="totalEmprestimos">0</span></div><div id="listaEmprestimos" class="lista-open-finance"></div></article>
                <article class="open-finance-bloco investimentos-bloco"><div class="titulo-open-finance"><h3>Investimentos</h3><span id="totalInvestimentos">0</span></div><div id="listaInvestimentos" class="lista-open-finance"></div></article>
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

        <form action="processa_movimentacao.php" method="POST">
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

// Dados do Open Finance
    const openFinanceStatus = document.getElementById("openFinanceStatus");
    const atualizarOpenFinance = document.getElementById("atualizarOpenFinance");
    const formatarMoeda = (valor) => new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(Number(valor || 0));
    const escapeHtml = (valor) => String(valor ?? "").replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));

    function renderizarLista(elemento, itens, tipo) {
        const total = document.getElementById(`total${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
        if (total) total.textContent = itens.length;
        if (!itens.length) { elemento.innerHTML = '<p class="sem-dados">Nenhum dado disponível.</p>'; return; }
        elemento.innerHTML = itens.slice(0, 10).map((item) => {
            const nome = item.description || item.name || item.type || "Sem descrição";
            const valor = item.amount ?? item.balance ?? item.currentAmount ?? item.value ?? 0;
            const data = item.date || item.dueDate || item.createdAt || item.updatedAt;
            const detalhe = tipo === "transacoes" ? (item.contaNome || item.category || "Conta conectada") : (item.institution || item.type || item.status || "Conta conectada");
            const icone = tipo === "transacoes" ? "↔" : tipo === "emprestimos" ? "¤" : "↗";
            const classeValor = tipo === "transacoes" && Number(valor) < 0 ? "valor-negativo" : "valor-positivo";
            const dataFormatada = data ? new Date(`${data}`.includes("T") ? data : `${data}T00:00:00`).toLocaleDateString("pt-BR") : "Atualizado agora";
            return `<div class="item-open-finance ${tipo}"><div class="icone-open-finance">${icone}</div><div class="descricao-open-finance"><strong>${escapeHtml(nome)}</strong><small>${escapeHtml(detalhe)} · ${escapeHtml(dataFormatada)}</small></div><span class="${classeValor}">${formatarMoeda(valor)}</span></div>`;
        }).join("");
    }

    async function carregarDadosOpenFinance() {
        openFinanceStatus.textContent = "Atualizando dados da conta conectada...";
        const destinos = { transacoes: document.getElementById("listaTransacoes"), emprestimos: document.getElementById("listaEmprestimos"), investimentos: document.getElementById("listaInvestimentos") };
        try {
            const respostas = await Promise.all(Object.keys(destinos).map(async (tipo) => {
                const resposta = await fetch(`../open-finance/${tipo}.php`, { credentials: "same-origin" });
                const dados = await resposta.json();
                if (!resposta.ok) throw new Error(dados.error || `Falha ao buscar ${tipo}`);
                return [tipo, dados[tipo] || []];
            }));
            respostas.forEach(([tipo, itens]) => renderizarLista(destinos[tipo], itens, tipo));
            openFinanceStatus.textContent = "Dados atualizados da sua conta conectada.";
        } catch (error) {
            Object.values(destinos).forEach((elemento) => elemento.innerHTML = '<p class="sem-dados">Não foi possível carregar os dados.</p>');
            openFinanceStatus.textContent = error.message;
            console.error("Erro ao carregar Open Finance:", error);
        }
        atualizarOpenFinance.hidden = false;
    }

    async function initPluggy() {
    try {
        // busca o connect-token no open-finance
        const response = await fetch("../open-finance/connect-token.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        //   envia uma requisição para o connect-token levando o valor de clienteUserId
        body: JSON.stringify({
            clientUserId: "bryan-001"
        })
        });

        const data = await response.json();

        // Valida se o token realmente veio na resposta
        const connectToken = data.accessToken || data.connectToken;

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
        const itemId = itemData?.item?.id;
        if (!itemId) throw new Error("A conexão não retornou o identificador da conta.");

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

        if (!resposta.ok || !resultado.success) throw new Error(resultado.error || "Não foi possível salvar a conta conectada.");
        await carregarDadosOpenFinance();
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

    atualizarOpenFinance.addEventListener("click", carregarDadosOpenFinance);
    carregarDadosOpenFinance();
</script>
<script src="https://cdn.pluggy.ai/pluggy-connect/latest/pluggy-connect.js"></script>

</body>
</html>
