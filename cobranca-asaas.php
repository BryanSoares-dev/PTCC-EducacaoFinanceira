<?php

require_once __DIR__ . '/back-end/seguranca.php';

// A chave da API nunca deve ficar hardcoded no código-fonte; ela agora
// vem da variável de ambiente ASAAS_API_KEY (definida no .env). O valor
// anterior aqui não era uma chave real do Asaas (formato inválido), mas
// o hábito de hardcodar segredos é o que importa corrigir.
$apiKey = afdeConfig('ASAAS_API_KEY', 'SUA_CHAVE_SANDBOX');

$url = 'https://api-sandbox.asaas.com/v3/payments';

$data = [
    'customer' => 'cus_000000000000',
    'billingType' => 'PIX',
    'value' => 50.00,
    'dueDate' => '2026-09-30',
    'description' => 'Plano AFDE'
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