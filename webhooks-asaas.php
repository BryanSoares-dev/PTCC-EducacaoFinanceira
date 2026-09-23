<?php

$dados = json_decode(
    file_get_contents('php://input'),
    true
);

$evento = $dados['event'] ?? null;

$pagamento = $dados['payment'] ?? null;

if ($evento === 'PAYMENT_RECEIVED') {

    $idPagamento = $pagamento['id'] ?? null;

    // Atualizar o pedido no banco
    // status = pago

}

http_response_code(200);

echo json_encode([
    'status' => 'ok'
]);