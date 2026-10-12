<?php
require_once __DIR__ . '/seguranca.php';
require_once __DIR__ . '/../conexao.php';

function responderJson(int $status, array $dados): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function lerEntrada(): array
{
    $json = json_decode(file_get_contents('php://input'), true);
    return is_array($json) ? $json : $_POST;
}

function usuarioLogadoId(): int
{
    $id = (int) ($_SESSION['id'] ?? 0);
    if ($id <= 0) {
        responderJson(401, ['erro' => 'Entre na sua conta para continuar.']);
    }
    return $id;
}

/** Valores autorizados no servidor; o navegador nunca define o valor cobrado. */
function afdePlanos(): array
{
    return [
        'premium' => [
            'mensal' => ['nome' => 'Plano Premium Mensal', 'valor' => 19.90],
            'anual' => ['nome' => 'Plano Premium Anual', 'valor' => 190.80],
        ],
        'pro' => [
            'mensal' => ['nome' => 'Plano Premium Pro Mensal', 'valor' => 39.90],
            'anual' => ['nome' => 'Plano Premium Pro Anual', 'valor' => 382.80],
        ],
    ];
}

function asaasRequest(string $metodo, string $caminho, ?array $dados = null): array
{
    $apiKey = afdeConfig('ASAAS_API_KEY', '');
    $base = rtrim(afdeConfig('ASAAS_BASE_URL', 'https://api-sandbox.asaas.com/v3'), '/');
    if ($apiKey === '') {
        throw new RuntimeException('ASAAS_API_KEY não configurada.');
    }
    if (!function_exists('curl_init')) {
        throw new RuntimeException('A extensão cURL do PHP não está habilitada.');
    }

    $ch = curl_init($base . '/' . ltrim($caminho, '/'));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => strtoupper($metodo),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'User-Agent: AFDE/1.0',
            'access_token: ' . $apiKey,
        ],
    ]);
    if ($dados !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
    $resposta = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erroCurl = curl_error($ch);
    curl_close($ch);
    if ($resposta === false) {
        throw new RuntimeException('Falha de conexão com o Asaas: ' . $erroCurl);
    }
    $json = json_decode($resposta, true);
    $json = is_array($json) ? $json : [];
    if ($status < 200 || $status >= 300) {
        $mensagem = $json['errors'][0]['description'] ?? 'Erro ao comunicar com o Asaas.';
        error_log("[asaas] HTTP $status em $metodo $caminho: $resposta");
        throw new RuntimeException($mensagem, $status);
    }
    return $json;
}

function buscarClienteAsaasLocal(int $usuarioId): ?string
{
    $st = afdeDb()->prepare('SELECT asaas_customer_id FROM clientes_asaas WHERE usuario_id = ? LIMIT 1');
    $st->execute([$usuarioId]);
    $id = $st->fetchColumn();
    return $id !== false ? (string) $id : null;
}

function garantirClienteAsaas(int $usuarioId, string $nome, string $email, string $cpfCnpj, ?string $telefone = null): string
{
    $existente = buscarClienteAsaasLocal($usuarioId);
    if ($existente !== null) {
        return $existente;
    }
    $dados = [
        'name' => $nome,
        'email' => $email,
        'cpfCnpj' => preg_replace('/\D+/', '', $cpfCnpj),
        'externalReference' => (string) $usuarioId,
    ];
    if ($telefone !== null && $telefone !== '') {
        $dados['mobilePhone'] = preg_replace('/\D+/', '', $telefone);
    }
    $cliente = asaasRequest('POST', '/customers', $dados);
    $customerId = (string) ($cliente['id'] ?? '');
    if ($customerId === '') {
        throw new RuntimeException('O Asaas não retornou o ID do cliente.');
    }
    try {
        $st = afdeDb()->prepare('INSERT INTO clientes_asaas (usuario_id, asaas_customer_id) VALUES (?, ?)');
        $st->execute([$usuarioId, $customerId]);
    } catch (PDOException $e) {
        // Duas requisições simultâneas podem criar o mesmo cliente; reutilize o que venceu.
        $existente = buscarClienteAsaasLocal($usuarioId);
        if ($existente !== null) {
            return $existente;
        }
        throw $e;
    }
    return $customerId;
}

function criarCobrancaAsaas(string $customerId, float $valor, string $vencimento, string $descricao, string $referenciaExterna): array
{
    return asaasRequest('POST', '/payments', [
        'customer' => $customerId,
        'billingType' => 'PIX',
        'value' => $valor,
        'dueDate' => $vencimento,
        'description' => $descricao,
        'externalReference' => $referenciaExterna,
    ]);
}

function buscarQrCodePix(string $paymentId): array
{
    return asaasRequest('GET', '/payments/' . rawurlencode($paymentId) . '/pixQrCode');
}
