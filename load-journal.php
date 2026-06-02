<?php
session_start();
header('Content-Type: application/json');
include 'config.php';

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'Not logged in. user_id missing.';
        echo json_encode($response);
        exit;
    }

    $user_id = (int)$_SESSION['user_id'];
    if ($user_id <= 0) {
        $response['message'] = 'Invalid user_id.';
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT id, title, content, entry_date, mood FROM journal_entries WHERE user_id = ? ORDER BY entry_date DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $response['message'] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }

    echo json_encode($entries); // Success = array

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>

