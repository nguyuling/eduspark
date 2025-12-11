<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database config — adjust path if needed
require_once __DIR__ . '/config/database.php'; // ← make sure this file exists & connects to your DB

// Get raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['user_id']) || !isset($data['game_id']) || !isset($data['score'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields: user_id, game_id, score"]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO game_scores (user_id, game_id, score, attempts, status)
        VALUES (?, ?, ?, ?, ?)
    ");

    $attempts = $data['attempts'] ?? 1;
    $status = $data['status'] ?? 'completed';

    $stmt->execute([
        $data['user_id'],
        $data['game_id'],
        $data['score'],
        $attempts,
        $status
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Score saved!",
        "score_id" => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>