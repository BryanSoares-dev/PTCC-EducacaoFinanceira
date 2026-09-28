<?php

require_once __DIR__ . '/back-end/seguranca.php';

// A chave da API nunca deve ficar hardcoded no código-fonte; ela agora
// vem da variável de ambiente ASAAS_API_KEY (definida no .env). O valor
// abaixo é apenas um placeholder de desenvolvimento, sem validade real.
$apiKey = afdeConfig('ASAAS_API_KEY', 'SUA_CHAVE_SANDBOX');

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