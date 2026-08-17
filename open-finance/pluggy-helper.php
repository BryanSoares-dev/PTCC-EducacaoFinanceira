<?php
/*
    Funções auxiliares reutilizadas pelos arquivos:
    investimentos.php, transacoes.php e emprestimos.php

    Mantém o mesmo padrão de autenticação usado no connect-token.php
*/

function pluggyCredentials() {
    return [
        'clientId' => getenv('PLUGGY_CLIENT_ID') ?: '3ac4dbf9-6de4-4b7e-8726-fb6a9b3050b2',
        'clientSecret' => getenv('PLUGGY_CLIENT_SECRET') ?: 'H6xMGJ6hPHOzM3QsVbrG_EWgtiI7lC5OrGkJXptxjXE'
    ];
}

// Autentica na Pluggy e retorna a apiKey (ou null em caso de falha)
function pluggyAuth() {
    $credentials = pluggyCredentials();

    $ch = curl_init('https://api.pluggy.ai/auth');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'clientId' => $credentials['clientId'],
            'clientSecret' => $credentials['clientSecret']
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
