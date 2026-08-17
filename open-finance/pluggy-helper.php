<?php

function pluggyCredentials(): array
{
    static $environment = null;

    if ($environment === null) {
        $environment = is_file(dirname(__DIR__) . '/.env')
            ? (parse_ini_file(dirname(__DIR__) . '/.env', false, INI_SCANNER_RAW) ?: [])
            : [];
    }

    return [
        'clientId' => getenv('PLUGGY_CLIENT_ID') ?: ($environment['PLUGGY_CLIENT_ID'] ?? ''),
        'clientSecret' => getenv('PLUGGY_CLIENT_SECRET') ?: ($environment['PLUGGY_CLIENT_SECRET'] ?? ''),
    ];
}

function pluggyJsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function pluggyRequest(string $method, string $path, ?string $apiKey = null, ?array $body = null): array
{
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($apiKey) {
        $headers[] = 'X-API-KEY: ' . $apiKey;
    }

    $url = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
        ? $path
        : 'https://api.pluggy.ai' . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $status,
        'data' => is_string($response) ? json_decode($response, true) : null,
        'error' => $error ?: null,
    ];
}

function pluggyAuth(): ?string
{
    $credentials = pluggyCredentials();
    if (!$credentials['clientId'] || !$credentials['clientSecret']) {
        return null;
    }

    $response = pluggyRequest('POST', '/auth', null, $credentials);
    return $response['code'] === 200 ? ($response['data']['apiKey'] ?? null) : null;
}

function pluggyGet(string $path, string $apiKey): array
{
    return pluggyRequest('GET', $path, $apiKey);
}

function getItemIdDoUsuario(PDO $pdo, int $usuarioId): ?string
{
    $stmt = $pdo->prepare('SELECT itemid FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $usuarioId]);
    $itemId = $stmt->fetchColumn();
    return is_string($itemId) && trim($itemId) !== '' ? trim($itemId) : null;
}

function pluggyResults(array $response): array
{
    return $response['code'] === 200 && is_array($response['data'])
        ? ($response['data']['results'] ?? [])
        : [];
}

function pluggyDadosDoItem(string $itemId, string $apiKey): array
{
    $encodedItemId = rawurlencode($itemId);
    $accountsResponse = pluggyGet('/accounts?itemId=' . $encodedItemId . '&pageSize=500', $apiKey);
    $transactions = [];

    foreach (pluggyResults($accountsResponse) as $account) {
        if (empty($account['id'])) {
            continue;
        }

        $response = pluggyGet('/transactions?accountId=' . rawurlencode($account['id']) . '&pageSize=500', $apiKey);
        foreach (pluggyResults($response) as $transaction) {
            $transaction['contaNome'] = $account['name'] ?? $account['marketingName'] ?? 'Conta conectada';
            $transactions[] = $transaction;
        }
    }

    usort($transactions, static fn(array $a, array $b): int => strtotime($b['date'] ?? '') <=> strtotime($a['date'] ?? ''));
    $loansResponse = pluggyGet('/loans?itemId=' . $encodedItemId . '&pageSize=500', $apiKey);
    $investmentsResponse = pluggyGet('/investments?itemId=' . $encodedItemId . '&pageSize=500', $apiKey);

    return [
        'transacoes' => $transactions,
        'emprestimos' => pluggyResults($loansResponse),
        'investimentos' => pluggyResults($investmentsResponse),
        'avisos' => array_values(array_filter([
            $accountsResponse['code'] !== 200 ? 'Não foi possível consultar as contas da instituição.' : null,
            $loansResponse['code'] !== 200 ? 'Empréstimos não estão disponíveis para esta instituição.' : null,
            $investmentsResponse['code'] !== 200 ? 'Investimentos não estão disponíveis para esta instituição.' : null,
        ])),
    ];
}
