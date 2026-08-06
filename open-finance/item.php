<?php
    session_start();
    require_once "../back-end/conexao.php"; // seu arquivo de conexão

    $body = json_decode(file_get_contents("php://input"), true);

    $itemId = $body["itemId"];
    $usuarioId = $_SESSION["id"]; // id do usuário logado

    $sql = "UPDATE usuarios
            SET itemid = :itemid
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":itemid" => $itemId,
        ":id" => $usuarioId
    ]);

    echo json_encode([
        "success" => true
    ]);
?>