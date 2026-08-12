<?php
    session_start();
    header("Content-Type: application/json; charset=utf-8");

    require_once "../back-end/conexao.php";
    require_once "pluggy-helper.php";

    if (!isset($_SESSION['id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário não autenticado']);
        exit;
    }

    $usuarioId = $_SESSION['id'];
    $itemId = getItemIdDoUsuario($pdo, $usuarioId);

    if (!$itemId) {
        echo json_encode(['error' => 'Nenhuma conta bancária conectada', 'transacoes' => []]);
        exit;
    }

    $apiKey = pluggyAuth();

    if (!$apiKey) {
        http_response_code(500);
        echo json_encode(['error' => 'Falha ao autenticar na Pluggy']);
        exit;
    }

    // 1. Busca as contas vinculadas ao item
    $contasResposta = pluggyGet(
        "https://api.pluggy.ai/accounts?itemId=" . urlencode($itemId),
        $apiKey
    );

    if ($contasResposta['code'] !== 200) {
        http_response_code($contasResposta['code']);
        echo json_encode(['error' => 'Falha ao buscar contas', 'details' => $contasResposta['data']]);
        exit;
    }

    $contas = $contasResposta['data']['results'] ?? [];

    // 2. Para cada conta, busca as transações e junta tudo em uma lista só
    $todasTransacoes = [];

    foreach ($contas as $conta) {
        $accountId = $conta['id'];

        $transResposta = pluggyGet(
            "https://api.pluggy.ai/transactions?accountId=" . urlencode($accountId) . "&pageSize=50",
            $apiKey
        );

        if ($transResposta['code'] === 200) {
            $transacoesDaConta = $transResposta['data']['results'] ?? [];

            foreach ($transacoesDaConta as $t) {
                $t['contaNome'] = $conta['name'] ?? 'Conta';
                $todasTransacoes[] = $t;
            }
        }
    }

    // Ordena as transações da mais recente para a mais antiga
    usort($todasTransacoes, function ($a, $b) {
        return strtotime($b['date'] ?? 'now') <=> strtotime($a['date'] ?? 'now');
    });

    echo json_encode([
        'transacoes' => $todasTransacoes
    ]);
    exit;
