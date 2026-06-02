<?php
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) { echo json_encode(["error" => "Not logged in"]); exit; }

require_once "config.php";

$stmt = $conn->prepare("INSERT INTO conversations (user_id, title) VALUES (?, 'New Chat')");
$stmt->bind_param("i", $userId);
$stmt->execute();
$conversationId = $conn->insert_id;
$stmt->close();
$conn->close();

echo json_encode(["conversation_id" => $conversationId]);
?>