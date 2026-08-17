<?php
    session_start();
    header("Content-Type: application/json; charset=utf-8");
    require_once "../back-end/conexao.php"; // seu arquivo de conexão

    $body = json_decode(file_get_contents("php://input"), true);

    $itemId = $body["itemId"] ?? null;
    $usuarioId = $_SESSION["id"]; // id do usuário logado

    if (empty($usuarioId) || empty($itemId)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Item ou usuário inválido"]);
        exit;
    }

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
