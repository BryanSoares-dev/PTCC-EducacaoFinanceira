<?php

session_start();

require_once '../back-end/conexao.php';


/* ============================================================
   PROTEÇÃO DA PÁGINA
============================================================ */

if (!isset($_SESSION['id'])) {

    header('Location: login.php');

    exit;
}


/* ============================================================
   BUSCAR USUÁRIO E TEMA
============================================================ */

$stmt = $pdo->prepare("
    SELECT id, nome, email, tema
    FROM usuarios
    WHERE id = ?
");

$stmt->execute([$_SESSION['id']]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$usuario) {

    session_destroy();

    header('Location: login.php');

    exit;
}


/* ============================================================
   TEMA
============================================================ */

$tema = $usuario['tema'] ?? ($_SESSION['tema'] ?? 'sistema');

$temas_permitidos = [
    'claro',
    'escuro',
    'sistema'
];

if (!in_array($tema, $temas_permitidos, true)) {

    $tema = 'sistema';

}


$usuarioId = (int) $usuario['id'];


/* ============================================================
   BUSCAR MOVIMENTAÇÕES
============================================================ */

$stmtMovimentacoes = $pdo->prepare("
    SELECT
        id,
        descricao,
        tipo,
        valor,
        data_criacao
    FROM movimentacoes
    WHERE usuario_id = ?
    ORDER BY data_criacao DESC, id DESC
");

$stmtMovimentacoes->execute([$usuarioId]);

$movimentacoes =
    $stmtMovimentacoes->fetchAll(PDO::FETCH_ASSOC);


/* ============================================================
   RESUMO FINANCEIRO
============================================================ */

$resumo = $pdo->prepare("
    SELECT

        COALESCE(
            SUM(
                CASE
                    WHEN tipo = 'entrada'
                    THEN valor
                    ELSE -valor
                END
            ),
            0
        ) AS saldo,

        COALESCE(
            SUM(
                CASE
                    WHEN tipo = 'entrada'
                    AND MONTH(data_criacao) = MONTH(CURDATE())
                    AND YEAR(data_criacao) = YEAR(CURDATE())
                    THEN valor
                    ELSE 0
                END
            ),
            0
        ) AS receitas_mes,

        COALESCE(
            SUM(
                CASE
                    WHEN tipo = 'saida'
                    AND MONTH(data_criacao) = MONTH(CURDATE())
                    AND YEAR(data_criacao) = YEAR(CURDATE())
                    THEN valor
                    ELSE 0
                END
            ),
            0
        ) AS despesas_mes

    FROM movimentacoes

    WHERE usuario_id = ?
");

$resumo->execute([$usuarioId]);

$dadosResumo =
    $resumo->fetch(PDO::FETCH_ASSOC) ?: [];


$saldo =
    (float) (
        $dadosResumo['saldo'] ?? 0
    );


$receitasMes =
    (float) (
        $dadosResumo['receitas_mes'] ?? 0
    );


$despesasMes =
    (float) (
        $dadosResumo['despesas_mes'] ?? 0
    );


$resultadoMes =
    $receitasMes - $despesasMes;


/* ============================================================
   FORMATAR MOEDA
============================================================ */

function formatarMoeda($valor)
{
    return 'R$ ' . number_format(
        $valor,
        2,
        ',',
        '.'
    );
}


/* ============================================================
   FORMATAR DATA
============================================================ */

function formatarData($data)
{
    if (empty($data)) {
        return '';
    }

    return date(
        'd/m/Y',
        strtotime($data)
    );
}

?>


<!DOCTYPE html>

<html
    lang="pt-BR"
    class="<?= htmlspecialchars($tema, ENT_QUOTES, 'UTF-8') ?>"
>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Carteira | FinControl</title>


    <link
        rel="stylesheet"
        href="../css/carteira.css"
    >


    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap"
        rel="stylesheet"
    >


    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
    >


    <link
        rel="icon"
        type="image/png"
        href="../img/favicon.png"
    >

</head>


<body>


<!-- ============================================================
     FUNDO
============================================================ -->

<div class="background_shapes">

    <div class="shape shape1"></div>

    <div class="shape shape2"></div>

    <div class="shape shape3"></div>

</div>


<!-- ============================================================
     BOTÃO VOLTAR
============================================================ -->

<a
    href="home.php"
    class="btn_voltar"
>

    <span class="material-icons">
        arrow_back
    </span>

    Voltar

</a>


<!-- ============================================================
     CONTEÚDO
============================================================ -->

<main class="carteira_container">


    <!-- ========================================================
         CABEÇALHO
    ========================================================= -->

    <section class="carteira_header glass">

        <div>

            <span class="carteira_eyebrow">
                Finanças
            </span>

            <h1>
                Minha carteira
            </h1>

            <p>
                Acompanhe seu saldo e suas movimentações.
            </p>

        </div>


        <div class="carteira_header_icon">

            <span class="material-icons">
                account_balance_wallet
            </span>

        </div>

    </section>


    <!-- ========================================================
         RESUMO
    ========================================================= -->

    <section class="resumo_grid">


        <!-- SALDO -->

        <div class="resumo_card glass-card resumo_card_saldo">

            <div class="resumo_card_top">

                <div class="resumo_icon">

                    <span class="material-icons">
                        account_balance_wallet
                    </span>

                </div>

                <span class="resumo_label">
                    Saldo atual
                </span>

            </div>


            <strong
                class="resumo_valor <?= $saldo < 0 ? 'valor_negativo' : 'valor_positivo' ?>"
            >

                <?= formatarMoeda($saldo) ?>

            </strong>

        </div>


        <!-- RECEITAS -->

        <div class="resumo_card glass-card">

            <div class="resumo_card_top">

                <div class="resumo_icon resumo_icon_receita">

                    <span class="material-icons">
                        trending_up
                    </span>

                </div>

                <span class="resumo_label">
                    Receitas do mês
                </span>

            </div>


            <strong class="resumo_valor valor_positivo">

                <?= formatarMoeda($receitasMes) ?>

            </strong>

        </div>


        <!-- DESPESAS -->

        <div class="resumo_card glass-card">

            <div class="resumo_card_top">

                <div class="resumo_icon resumo_icon_despesa">

                    <span class="material-icons">
                        trending_down
                    </span>

                </div>

                <span class="resumo_label">
                    Despesas do mês
                </span>

            </div>


            <strong class="resumo_valor valor_negativo">

                <?= formatarMoeda($despesasMes) ?>

            </strong>

        </div>


        <!-- RESULTADO -->

        <div class="resumo_card glass-card">

            <div class="resumo_card_top">

                <div class="resumo_icon resumo_icon_resultado">

                    <span class="material-icons">
                        analytics
                    </span>

                </div>

                <span class="resumo_label">
                    Resultado do mês
                </span>

            </div>


            <strong
                class="resumo_valor <?= $resultadoMes < 0 ? 'valor_negativo' : 'valor_positivo' ?>"
            >

                <?= formatarMoeda($resultadoMes) ?>

            </strong>

        </div>

    </section>


    <!-- ========================================================
         MOVIMENTAÇÕES
    ========================================================= -->

    <section class="movimentacoes_section">


        <div class="section_header">

            <div>

                <h2>
                    Movimentações
                </h2>

                <p>
                    Histórico financeiro da sua conta
                </p>

            </div>


            <a
                href="movimentacoes.php"
                class="btn_nova_movimentacao"
            >

                <span class="material-icons">
                    add
                </span>

                Nova movimentação

            </a>

        </div>


        <div class="movimentacoes_card glass-card">


            <?php if (empty($movimentacoes)): ?>


                <div class="empty_state">

                    <div class="empty_icon">

                        <span class="material-icons">
                            account_balance_wallet
                        </span>

                    </div>


                    <h3>
                        Nenhuma movimentação
                    </h3>


                    <p>
                        Suas entradas e saídas aparecerão aqui.
                    </p>


                    <a
                        href="movimentacoes.php"
                        class="btn_empty"
                    >

                        Adicionar movimentação

                    </a>

                </div>


            <?php else: ?>


                <div class="movimentacoes_lista">


                    <?php foreach ($movimentacoes as $movimentacao): ?>


                        <?php

                        $tipo =
                            strtolower(
                                $movimentacao['tipo'] ?? ''
                            );

                        $isEntrada =
                            $tipo === 'entrada';

                        ?>


                        <div class="movimentacao_item">


                            <div
                                class="
                                    movimentacao_icon
                                    <?= $isEntrada
                                        ? 'movimentacao_entrada'
                                        : 'movimentacao_saida'
                                    ?>
                                "
                            >

                                <span class="material-icons">

                                    <?= $isEntrada
                                        ? 'arrow_downward'
                                        : 'arrow_upward'
                                    ?>

                                </span>

                            </div>


                            <div class="movimentacao_info">

                                <h3>

                                    <?= htmlspecialchars(
                                        $movimentacao['descricao']
                                            ?: 'Movimentação',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </h3>


                                <p>

                                    <?= $isEntrada
                                        ? 'Entrada'
                                        : 'Saída'
                                    ?>

                                    <span>
                                        •
                                    </span>

                                    <?= formatarData(
                                        $movimentacao['data_criacao']
                                    ) ?>

                                </p>

                            </div>


                            <strong
                                class="
                                    movimentacao_valor
                                    <?= $isEntrada
                                        ? 'valor_positivo'
                                        : 'valor_negativo'
                                    ?>
                                "
                            >

                                <?= $isEntrada ? '+' : '-' ?>

                                <?= formatarMoeda(
                                    (float) $movimentacao['valor']
                                ) ?>

                            </strong>


                        </div>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        </div>

    </section>


</main>


</body>

</html>
