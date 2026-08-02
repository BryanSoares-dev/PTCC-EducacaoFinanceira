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

                <button class="btn-movimentacao" onclick="abrirModal()">
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

                <select name="tipo" required>
                    <option value="" selected disabled>Selecione</option>
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                </select>
            </div>

            <div class="form-group">
                <label>Categoria</label>

                <select name="categoria" required>
                    <option value="" selected disabled>Selecione uma categoria</option>

                    <option value="Alimentação">🍔 Alimentação</option>
                    <option value="Transporte">🚗 Transporte</option>
                    <option value="Moradia">🏠 Moradia</option>
                    <option value="Saúde">🏥 Saúde</option>
                    <option value="Educação">📚 Educação</option>
                    <option value="Lazer">🎮 Lazer</option>
                    <option value="Investimentos">📈 Investimentos</option>
                    <option value="Salário">💰 Salário</option>
                    <option value="Freelance">💻 Freelance</option>
                    <option value="Presente">🎁 Presente</option>
                    <option value="Outros">📦 Outros</option>
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

<script src="js/carteira.js"></script>

</body>
</html>