<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';


if (!isset($_SESSION['id'])) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado.'
    ]);

    exit;
}


$usuarioId = (int) $_SESSION['id'];


/* ==========================================
   RECEBER MÊS E ANO
========================================== */

$mes = isset($_GET['mes'])
    ? (int) $_GET['mes']
    : (int) date('m');


$ano = isset($_GET['ano'])
    ? (int) $_GET['ano']
    : (int) date('Y');


if ($mes < 1 || $mes > 12) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Mês inválido.'
    ]);

    exit;
}


/* ==========================================
   DADOS DA CONTA
========================================== */

$stmtUsuario = $pdo->prepare("
    SELECT data_criacao
    FROM usuarios
    WHERE id = ?
");

$stmtUsuario->execute([$usuarioId]);

$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);


if (!$usuario) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não encontrado.'
    ]);

    exit;
}


$dataCriacao = new DateTime($usuario['data_criacao']);

$primeiroMes = (int) $dataCriacao->format('m');
$primeiroAno = (int) $dataCriacao->format('Y');


/* ==========================================
   NÃO DEIXAR ACESSAR ANTES DA CONTA EXISTIR
========================================== */

if (
    $ano < $primeiroAno ||
    (
        $ano === $primeiroAno &&
        $mes < $primeiroMes
    )
) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Esse mês é anterior à criação da conta.'
    ]);

    exit;
}


/* ==========================================
   NÃO DEIXAR ACESSAR FUTURO
========================================== */

$dataAtual = new DateTime();

if (
    $ano > (int) $dataAtual->format('Y') ||
    (
        $ano === (int) $dataAtual->format('Y') &&
        $mes > (int) $dataAtual->format('m')
    )
) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Não existem dados futuros.'
    ]);

    exit;
}


/* ==========================================
   INTERVALO DO MÊS
========================================== */

$dataInicio = sprintf(
    '%04d-%02d-01 00:00:00',
    $ano,
    $mes
);


$proximoMes = new DateTime(
    sprintf(
        '%04d-%02d-01',
        $ano,
        $mes
    )
);

$proximoMes->modify('+1 month');


$dataFim = $proximoMes->format(
    'Y-m-d 00:00:00'
);


/* ==========================================
   BUSCAR MOVIMENTAÇÕES
========================================== */

$stmt = $pdo->prepare("
    SELECT
        id,
        descricao,
        tipo,
        valor,
        categoria,
        data_criacao
    FROM movimentacoes
    WHERE usuario_id = ?
    AND data_criacao >= ?
    AND data_criacao < ?
    ORDER BY data_criacao ASC
");


$stmt->execute([
    $usuarioId,
    $dataInicio,
    $dataFim
]);


$movimentacoes = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);


/* ==========================================
   AGRUPAR DESPESAS POR CATEGORIA
========================================== */

$categorias = [];

$receitas = 0;
$despesas = 0;


foreach ($movimentacoes as $movimentacao) {

    $valor = (float) $movimentacao['valor'];

    $tipo = strtolower(
        $movimentacao['tipo']
    );


    if ($tipo === 'entrada') {

        $receitas += $valor;

    } else {

        $despesas += $valor;


        $categoria = trim(
            $movimentacao['categoria']
            ?? ''
        );


        if ($categoria === '') {

            $categoria = 'Outros';

        }


        if (!isset($categorias[$categoria])) {

            $categorias[$categoria] = 0;

        }


        $categorias[$categoria] += $valor;

    }

}


/* ==========================================
   FORMATAR CATEGORIAS
========================================== */

$labels = [];
$valores = [];


foreach ($categorias as $categoria => $valor) {

    $labels[] = $categoria;

    $valores[] = round(
        $valor,
        2
    );

}


/* ==========================================
   RESPOSTA JSON
========================================== */

echo json_encode([

    'sucesso' => true,


    'periodo' => [

        'mes' => $mes,

        'ano' => $ano

    ],


    'conta' => [

        'mes_criacao' => $primeiroMes,

        'ano_criacao' => $primeiroAno

    ],


    'grafico' => [

        'labels' => $labels,

        'valores' => $valores

    ],


    'resumo' => [

        'receitas' => round(
            $receitas,
            2
        ),

        'despesas' => round(
            $despesas,
            2
        ),

        'resultado' => round(
            $receitas - $despesas,
            2
        )

    ],


    'quantidade' => count(
        $movimentacoes
    )

]);
