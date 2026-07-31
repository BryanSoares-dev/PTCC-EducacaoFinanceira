    <?php

    header('Content-Type: application/json; charset=utf-8');


    $input = file_get_contents('php://input');
    $event = json_decode($input, true);

    if (!$event || !is_array($event)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid JSON payload']);
        exit;
    }

    $eventType = $event['event'] ?? 'unknown';
    $eventId   = $event['eventId'] ?? 'N/A';
    $itemId    = $event['itemId'] ?? ($event['data']['id'] ?? null); // Garante a captura do itemId

    error_log("Received webhook: {$eventType}");
    error_log("Event ID: {$eventId}");

    switch ($eventType) {
        case 'item/created':
            if ($itemId) handleItemCreated($itemId);
            break;

        case 'item/updated':
            if ($itemId) handleItemUpdated($itemId);
            break;

        case 'item/error':
            if ($itemId) {
                $error = $event['error'] ?? ($event['data']['error'] ?? null);
                handleItemError($itemId, $error);
            }
            break;

        default:

            error_log("Unhandled event type: {$eventType}");
            break;
    }

    http_response_code(200);
    echo json_encode(['received' => true]);
    exit;