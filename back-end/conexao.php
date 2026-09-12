<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('DB_PORT') ?: 3307);
$db   = getenv('DB_NAME') ?: 'educacaofinanceira';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]
    );
} catch (PDOException $e) {
    error_log(sprintf('Falha PDO [%s] host=%s porta=%d banco=%s: %s', $e->getCode(), $host, $port, $db, $e->getMessage()));
    http_response_code(503);
    exit('Não foi possível conectar ao banco de dados. Verifique o serviço MySQL/MariaDB, a porta 3307 e o banco configurado.');
}
