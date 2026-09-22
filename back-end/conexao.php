<?php
$host = "127.0.0.1";
$port = 3306; // ajuste aqui se o seu MySQL usar outra porta (verifique no XAMPP/Laragon)
$db   = "educacaofinanceira";
$user = "root";
$pass = "";

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}


?>
