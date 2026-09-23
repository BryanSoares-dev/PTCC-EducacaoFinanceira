<?php

$apiKey = 'SUA_CHAVE_SANDBOX';

$url = 'https://api-sandbox.asaas.com/v3/customers';

$data = [
    'name' => 'João da Silva',
    'cpfCnpj' => '12345678900',
    'email' => 'joao@email.com'
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: AFDE/1.0',
    'access_token: ' . $apiKey
]);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
);

$response = curl_exec($ch);

curl_close($ch);

echo $response;