<?php
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) { echo json_encode([]); exit; }

require_once "config.php";

$stmt = $conn->prepare("SELECT id, title FROM conversations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$conversations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

echo json_encode($conversations);
?>