<?php

$apiKey = 'bryan-zika-chave';

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