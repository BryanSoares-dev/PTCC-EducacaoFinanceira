<?php
/**
 * Ponte same-origin do assistente. O navegador nunca precisa conhecer a
 * porta interna da API nem fazer uma chamada cross-origin/CORS.
 */
require_once __DIR__ . '/seguranca.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '', true);
$pergunta = is_array($payload) ? trim((string)($payload['pergunta'] ?? '')) : '';

if ($pergunta === '' || mb_strlen($pergunta) > 1000) {
    http_response_code(400);
    echo json_encode(['erro' => 'Envie uma pergunta válida de até 1.000 caracteres.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$apiUrl = rtrim(afdeConfig('CHATBOT_API_URL', 'http://127.0.0.1:5000'), '/') . '/api/perguntar';
if (!filter_var($apiUrl, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $apiUrl)) {
    http_response_code(500);
    echo json_encode(['erro' => 'O serviço de atendimento não está configurado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!function_exists('curl_init')) {
    http_response_code(503);
    echo json_encode(['erro' => 'O serviço de atendimento está indisponível neste servidor.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$curl = curl_init($apiUrl);
$ipCliente = function_exists('obter_ip_cliente') ? obter_ip_cliente() : (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
if (!filter_var($ipCliente, FILTER_VALIDATE_IP)) {
    $ipCliente = 'unknown';
}
curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['pergunta' => $pergunta], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json', 'X-AFDE-Client-IP: ' . $ipCliente],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 35,
    CURLOPT_FOLLOWLOCATION => false,
]);
$response = curl_exec($curl);
$status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);
curl_close($curl);

if ($response === false || $curlError !== '') {
    http_response_code(503);
    echo json_encode(['erro' => 'Não foi possível conectar ao assistente. Verifique se o serviço está iniciado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$decoded = json_decode($response, true);
if (!is_array($decoded)) {
    http_response_code(502);
    echo json_encode(['erro' => 'O assistente retornou uma resposta inválida.'], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code($status >= 400 ? $status : 200);
echo json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
