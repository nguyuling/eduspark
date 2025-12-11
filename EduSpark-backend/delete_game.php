<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: PUT, DELETE, GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Game ID required"]);
    exit;
}

try {
    // Optional: Check if game exists first
    $check = $pdo->prepare("SELECT id FROM games WHERE id = ?");
    $check->execute([$data['id']]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "Game not found"]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
    $stmt->execute([$data['id']]);

    echo json_encode([
        "success" => true,
        "message" => "Game deleted",
        "id" => $data['id']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>