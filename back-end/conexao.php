<?php
/**
 * Adaptador de compatibilidade para o backend legado.
 *
 * A conexão central fica em /conexao.php. Este arquivo mantém a variável
 * $pdo esperada pelos endpoints existentes, evitando dois DSNs e dois
 * conjuntos de defaults diferentes dentro do mesmo projeto.
 */
require_once dirname(__DIR__) . '/conexao.php';

$pdo = afdeDb();
