<?php

require_once __DIR__ . '/seguranca.php';

/**
 * conexao.php
 * ------------------------------------------------------------------
 * Problema: as credenciais do banco estavam fixas diretamente no
 * código-fonte. Mesmo sendo credenciais de desenvolvimento local
 * (root/sem senha, como no XAMPP/Laragon), isso é uma má prática: se o
 * arquivo for versionado ou vazado, ou se em produção alguém apenas
 * copiar este arquivo sem trocar as credenciais, o banco fica exposto.
 *
 * Solução: as credenciais agora vêm de variáveis de ambiente (ou do
 * arquivo .env, já usado pelo restante do projeto para as integrações
 * Pluggy/Google), com os mesmos valores de desenvolvimento como
 * fallback — quem já usa o projeto localmente no XAMPP/Laragon não
 * precisa mudar nada, mas quem for para produção deve definir
 * DB_HOST / DB_PORT / DB_NAME / DB_USER / DB_PASS no .env.
 */

$host = afdeConfig('DB_HOST', '127.0.0.1');
$port = (int) afdeConfig('DB_PORT', '3306'); // porta padrão do MySQL/MariaDB
$db   = afdeConfig('DB_NAME', 'educacaofinanceira');
$user = afdeConfig('DB_USER', 'root');
$pass = afdeConfig('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Problema: mostrar $e->getMessage() ao visitante pode expor host,
    // nome do banco e detalhes de configuração do servidor.
    // Solução: log no servidor + mensagem genérica na tela.
    error_log('[conexao] Falha ao conectar ao banco: ' . $e->getMessage());
    http_response_code(500);
    die("Não foi possível conectar ao banco de dados no momento. Tente novamente em instantes.");
}
