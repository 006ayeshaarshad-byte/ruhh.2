<?php
session_start();
header("Content-Type: application/json");

require_once "config.php";

$stmt = $conn->prepare("SELECT content, created_at FROM community_posts ORDER BY created_at DESC LIMIT 50");
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

echo json_encode($posts);
?>