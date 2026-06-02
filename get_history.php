<?php
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) { echo json_encode([]); exit; }

$conversationId = intval($_GET["conversation_id"] ?? 0);
if (!$conversationId) { echo json_encode([]); exit; }

require_once "config.php";

$check = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $conversationId, $userId);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) { echo json_encode([]); exit; }
$check->close();

$stmt = $conn->prepare("SELECT role, message FROM chat_messages WHERE conversation_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $conversationId);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

echo json_encode($messages);
?>







