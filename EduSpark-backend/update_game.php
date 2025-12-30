<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "No data"]);
    exit;
}

// Handle update
if (isset($input['id']) && isset($input['title'])) {
    echo json_encode([
        "success" => true,
        "message" => "Game updated",
        "id" => $input['id']
    ]);
}
// Handle delete
elseif (isset($input['action']) && $input['action'] === 'delete' && isset($input['id'])) {
    echo json_encode([
        "success" => true,
        "message" => "Game deleted",
        "id" => $input['id']
    ]);
}
else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
}
?>