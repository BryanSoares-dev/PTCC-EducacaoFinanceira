<?php

require_once __DIR__ . '/back-end/seguranca.php';

/** Retorna uma conexão PDO única para o projeto. */
function afdeDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            afdeConfig('DB_HOST', '127.0.0.1'),
            afdeConfig('DB_PORT', '3307'),
            afdeConfig('DB_NAME', 'educacaofinanceira')
        );

        $pdo = new PDO($dsn, afdeConfig('DB_USER', ''), afdeConfig('DB_PASS', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}
