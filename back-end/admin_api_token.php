<?php
/** Token curto para a API Flask administrativa. Deve ser chamado somente após admin_auth.php. */
function afde_admin_api_token(int $adminId): string
{
    $secret = afdeConfig('AFDE_ADMIN_API_SECRET', 'dev-only-change-this-secret-before-production-8f3c');
    $payload = json_encode([
        'sub' => $adminId,
        'exp' => time() + 900,
    ], JSON_UNESCAPED_SLASHES);
    $encoded = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    $signature = hash_hmac('sha256', $encoded, $secret, true);
    return $encoded . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
}
