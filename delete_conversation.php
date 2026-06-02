<?php
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) { echo json_encode(["success" => false]); exit; }

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$conversationId = intval($data["conversation_id"] ?? 0);

if (!$conversationId) { echo json_encode(["success" => false]); exit; }

require_once "config.php";

$stmt = $conn->prepare("DELETE FROM conversations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $conversationId, $userId);
$stmt->execute();
$deleted = $stmt->affected_rows > 0;
$stmt->close();
$conn->close();

echo json_encode(["success" => $deleted]);
?>