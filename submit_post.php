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
$content = trim($data["content"] ?? "");

if ($content === "") {
    echo json_encode(["success" => false, "error" => "Empty content"]);
    exit;
}

// Profanity filter
$badWords = [
    'fuck', 'shit', 'bitch', 'cunt', 'damn', 'ass', 'piss', 'dick', 
    'cock', 'pussy', 'whore', 'slut', 'bastard', 'fag', 'nigger', 
    'retard', 'rape', 'asshole', 'motherfucker', 'faggot' , 'kutta' , 'lanti' , 'lanat' , 'haramzada' , 'haramzadi' , 'kutti' , 'bhenchod' , 'maachod' , 'bhencho' , 'maacho' ,  'fck' , 'f@k' , 'bit@h' , 'nigga' , 'nigg@' , 'fuckk' , 
];

$contentLower = strtolower($content);
foreach ($badWords as $word) {
    if (strpos($contentLower, $word) !== false) {
        echo json_encode([
            "success" => false, 
            "error" => "Your post contains inappropriate language. Please keep it respectful 💜"
        ]);
        exit;
    }
}

// Check word count
$words = str_word_count($content);
if ($words > 150) {
    echo json_encode(["success" => false, "error" => "Exceeds 150 words"]);
    exit;
}

require_once "config.php";

$stmt = $conn->prepare("INSERT INTO community_posts (user_id, content) VALUES (?, ?)");
$stmt->bind_param("is", $userId, $content);
$success = $stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(["success" => $success]);
?> 