<?php
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$postId = intval($data["post_id"] ?? 0);

if (!$postId) {
    echo json_encode(["success" => false, "error" => "Invalid post ID"]);
    exit;
}

require_once "config.php";

// Delete only if this post belongs to this user
$stmt = $conn->prepare("DELETE FROM community_posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$deleted = $stmt->affected_rows > 0;

$stmt->close();
$conn->close();

echo json_encode(["success" => $deleted]);
?>