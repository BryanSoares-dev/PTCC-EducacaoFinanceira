<?php

require_once 'seguranca.php';

/**
 * Retorna uma conexão PDO única (reaproveitada em toda a requisição).
 * Se seu projeto já tem uma conexão, troque o conteúdo desta função
 * para devolver a sua.
 */
function afdeDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            afdeConfig('DB_HOST', 'localhost'),
            afdeConfig('DB_NAME', '')
        );

        $pdo = new PDO($dsn, afdeConfig('DB_USER', ''), afdeConfig('DB_PASS', ''), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}
