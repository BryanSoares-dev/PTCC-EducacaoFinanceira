    <?php
    session_start();
    require_once("conexao.php");

    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/carteira.css">
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
    <script src="https://cdn.pluggy.ai/pluggy-connect/latest/pluggy-connect.js"></script>
    <script type="module">
    async function initPluggy() {
    try {
        // busca o connect-token no open-finance
        const response = await fetch("./open-finance/connect-token.php", {
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
        const connectToken = data.accessToken || data.accessToken; 
        
        if (!response.ok || !connectToken) {
        throw new Error(data.error || "Falha ao obter o connectToken do servidor.");
        }

        if (typeof window.PluggyConnect !== 'function') {
        throw new Error("A biblioteca PluggyConnect não foi carregada no HTML.");
        }

        const pluggyConnect = new window.PluggyConnect({
        connectToken: connectToken,
        includeSandbox: true,
        onSuccess: (itemData) => {
            console.log('Conectado com sucesso!', itemData);

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

    initPluggy();
    </script>

</body>
</html>