<?php
require_once __DIR__ . '/seguranca.php';
iniciar_sessao_segura();
require_once __DIR__ . '/conexao.php';

function google_config(string $key, string $default = ''): string {
    return afdeConfig($key, $default);
}

function google_redirect_uri(): string {
    $configured = trim(google_config('GOOGLE_REDIRECT_URL'));
    if ($configured !== '') return $configured;

    $https = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) == 443)
        || strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    $scheme = $https ? 'https' : 'http';
    $host = (string)($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
    $host = preg_replace('/[^a-zA-Z0-9.:[\]-]/', '', $host);
    return $scheme . '://' . $host . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/back-end'), '/') . '/google-callback.php';
}

function google_http_post(string $url, array $fields): array {
    if (!function_exists('curl_init')) throw new RuntimeException('cURL não está disponível.');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields, '', '&', PHP_QUERY_RFC3986),
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($body === false || $error !== '') throw new RuntimeException('Falha na comunicação com o Google.');
    $json = json_decode($body, true);
    if (!is_array($json) || $status < 200 || $status >= 300) throw new RuntimeException('O Google recusou a autenticação.');
    return $json;
}

function google_http_get(string $url, string $accessToken): array {
    if (!function_exists('curl_init')) throw new RuntimeException('cURL não está disponível.');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'Authorization: Bearer ' . $accessToken],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($body === false || $error !== '' || $status < 200 || $status >= 300) throw new RuntimeException('Não foi possível obter o perfil Google.');
    $json = json_decode($body, true);
    if (!is_array($json)) throw new RuntimeException('Resposta inválida do Google.');
    return $json;
}

function google_falhar(string $mensagem): never {
    $_SESSION['mensagem'] = $mensagem;
    header('Location: ../front-end/login.php');
    exit;
}

$clientId = google_config('GOOGLE_CLIENT_ID');
$clientSecret = google_config('GOOGLE_CLIENT_SECRET');
$redirectUri = google_redirect_uri();
if ($clientId === '' || $clientSecret === '') google_falhar('Login com Google ainda não está configurado.');

if (isset($_GET['error'])) google_falhar('O login com Google foi cancelado ou não autorizado.');

if (!isset($_GET['code'])) {
    $state = bin2hex(random_bytes(32));
    $_SESSION['google_oauth_state'] = $state;
    $params = [
        'client_id' => $clientId,
        'redirect_uri' => $redirectUri,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'access_type' => 'online',
        'include_granted_scopes' => 'true',
        'prompt' => 'select_account',
    ];
    header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    exit;
}

$stateRecebido = (string)($_GET['state'] ?? '');
$stateEsperado = (string)($_SESSION['google_oauth_state'] ?? '');
unset($_SESSION['google_oauth_state']);
if ($stateEsperado === '' || $stateRecebido === '' || !hash_equals($stateEsperado, $stateRecebido)) {
    http_response_code(403);
    exit('Não foi possível validar esta tentativa de login com o Google.');
}

try {
    $tokens = google_http_post('https://oauth2.googleapis.com/token', [
        'code' => (string)$_GET['code'],
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code',
    ]);
    $accessToken = (string)($tokens['access_token'] ?? '');
    if ($accessToken === '') throw new RuntimeException('Token de acesso ausente.');
    $userInfo = google_http_get('https://openidconnect.googleapis.com/v1/userinfo', $accessToken);
    $email = strtolower(trim((string)($userInfo['email'] ?? '')));
    $googleId = trim((string)($userInfo['sub'] ?? ''));
    if ($email === '' || $googleId === '' || empty($userInfo['email_verified'])) throw new RuntimeException('Conta Google sem e-mail verificado.');

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $existente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existente) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (oauth_uid, nome, email, foto, provedor) VALUES (?, ?, ?, ?, 'google')");
        $stmt->execute([$googleId, (string)($userInfo['name'] ?? $email), $email, (string)($userInfo['picture'] ?? '')]);
        $idUsuario = (int)$pdo->lastInsertId();
    } else {
        if (!empty($existente['oauth_uid']) && !hash_equals((string)$existente['oauth_uid'], $googleId)) throw new RuntimeException('Identidade Google divergente.');
        if (empty($existente['oauth_uid'])) google_falhar('Esta conta já existe. Entre com e-mail e senha para continuar.');
        $idUsuario = (int)$existente['id'];
    }

    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['id'] = $idUsuario;
    $_SESSION['usuario'] = ['id' => $idUsuario, 'nome' => (string)($userInfo['name'] ?? $email), 'email' => $email, 'foto' => (string)($userInfo['picture'] ?? '')];
    $_SESSION['nome'] = (string)($userInfo['name'] ?? $email);
    $_SESSION['email'] = $email;
    $_SESSION['foto'] = (string)($userInfo['picture'] ?? '');
    header('Location: ../front-end/home.php');
    exit;
} catch (Throwable $e) {
    error_log('[google-callback] ' . get_class($e) . ': ' . $e->getMessage());
    google_falhar('Não foi possível concluir o login com o Google. Tente novamente.');
}
