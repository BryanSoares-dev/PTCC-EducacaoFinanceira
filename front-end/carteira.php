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
</script>

</body>
</html>