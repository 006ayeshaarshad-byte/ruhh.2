<?php
session_start();
header('Content-Type: application/json');
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$entry_date = trim($_POST['entry_date'] ?? '');
$mood = trim($_POST['mood'] ?? '');

if (empty($title) || empty($content) || empty($entry_date) || empty($mood)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$sql = "INSERT INTO journal_entries (user_id, title, content, entry_date, mood) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("issss", $user_id, $title, $content, $entry_date, $mood);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'saved']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

