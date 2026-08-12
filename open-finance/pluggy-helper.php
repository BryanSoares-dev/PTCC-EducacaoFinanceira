<?php
/*
    Funções auxiliares reutilizadas pelos arquivos:
    investimentos.php, transacoes.php e emprestimos.php

    Mantém o mesmo padrão de autenticação usado no connect-token.php
*/

// Autentica na Pluggy e retorna a apiKey (ou null em caso de falha)
function pluggyAuth() {
    $clientId = "c4aa89d1-cda5-4da8-8db9-b7ac59fb5bf0";
    $clientSecret = "kNrUIgFWx__xGEwuk-Lfacw5Nd9G1cfVU84HKHr1OXs";

    $ch = curl_init('https://api.pluggy.ai/auth');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($httpCode !== 200 || empty($data['apiKey'])) {
        return null;
    }

    return $data['apiKey'];
}

// Faz uma requisição GET autenticada na API da Pluggy
function pluggyGet($url, $apiKey) {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "X-API-KEY: $apiKey",
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Busca o itemId salvo do usuário logado (mesma coluna usada no item.php)
function getItemIdDoUsuario($pdo, $usuarioId) {
    $sql = "SELECT itemid FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $usuarioId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['itemid'] ?? null;
}
