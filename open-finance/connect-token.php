<?php
// para que o formato sempre seja json
    header("Content-Type: application/json; charset=utf-8");

// api key
    require_once "pluggy-helper.php";

// pega a requisição enviada pelo front (no codigo js) e armazena dentro "diretório" php://input
    $inputJson = file_get_contents('php://input');

// como o $inputJson ainda é so uma string é preciso converte-lo para um array
    $body = json_decode($inputJson, true);

// verifica se o valor de clientUserId foi enviado, caso o js envie por exemplo {} o valor será null
    $clientUserId = $body['clientUserId'] ?? null;

// função auxiliar para requisições cURL
    function callPluggyApi($url, $data, $headers = []) {
        $ch = curl_init($url);

        $defaultHeaders = ['Content-Type: application/json'];
        $allHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $allHeaders
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

// é como se o php demonstrasse pra api que possui a key
    $authResponse = callPluggyApi('https://api.pluggy.ai/auth', pluggyCredentials());

// retorno da api
    if ($authResponse['code'] !== 200 || empty($authResponse['data']['apiKey'])) {
        http_response_code($authResponse['code'] ?: 500);
        echo json_encode([
            'error' => 'Falha ao autenticar na Pluggy (Obter API Key)',
            'details' => $authResponse['data']
        ]);
        exit;
    }

    $apiKey = $authResponse['data']['apiKey'];


    $connectPayload = [];
    if ($clientUserId) {
        $connectPayload['options'] = [
            'clientUserId' => $clientUserId
        ];
    }

    $tokenResponse = callPluggyApi(
        'https://api.pluggy.ai/connect_token',
        $connectPayload,
        ["X-API-KEY: $apiKey"]
    );

    http_response_code($tokenResponse['code']);
    echo json_encode($tokenResponse['data']);
    exit;
