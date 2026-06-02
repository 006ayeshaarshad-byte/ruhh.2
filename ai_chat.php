<?php
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) { echo json_encode(["reply" => "Please log in first."]); exit; }

$apiKey = "AIzaSyCMizx-zk4Ar__DuaPraYyojo_9qpgK_Is";

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);
$userMessage    = isset($data["message"]) ? trim($data["message"]) : "";
$conversationId = intval($data["conversation_id"] ?? 0);

if ($userMessage === "") { echo json_encode(["reply" => "I didn't catch that 💜"]); exit; }
if (!$conversationId)    { echo json_encode(["reply" => "No conversation found."]); exit; }

require_once "config.php";

// ── Set title from first message ──────────────────────────────────────────
$countStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM chat_messages WHERE conversation_id = ?");
$countStmt->bind_param("i", $conversationId);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$countStmt->close();

if ($countResult["cnt"] == 0) {
    $title = mb_substr($userMessage, 0, 60);
    $titleStmt = $conn->prepare("UPDATE conversations SET title = ? WHERE id = ? AND user_id = ?");
    $titleStmt->bind_param("sii", $title, $conversationId, $userId);
    $titleStmt->execute();
    $titleStmt->close();
}

// ── Load conversation history (last 10 exchanges = 20 messages) ────────────
$histStmt = $conn->prepare(
    "SELECT role, message FROM chat_messages
     WHERE conversation_id = ? AND user_id = ?
     ORDER BY id DESC LIMIT 20"
);
$histStmt->bind_param("ii", $conversationId, $userId);
$histStmt->execute();
$histResult = $histStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$histStmt->close();
$histResult = array_reverse($histResult); // oldest first

// ── Build Gemini contents array with history ──────────────────────────────
$systemPrompt = "You are Luna, a soft, sweet, funny, not uptight,  bubbly, warm, and emotionally intelligent AI therapist built into the Ruhh mental wellness platform.
You respond with empathy, validation, and gentle guidance. Never judge. Keep replies warm and concise.
Always end with a heart emoji 💜. If the user asks your name, say it is Luna, and they can give you a nickname if they like.
If the user mentions self-harm or suicidal thoughts, respond with deep compassion and refer them to the helpline section in ruhh website, which has resources and numbers they can call for support. 
Remember the conversation context — you have a memory of this session.";

$contents = [];

// Add system context as first user+model turn
$contents[] = ["role" => "user",  "parts" => [["text" => $systemPrompt]]];
$contents[] = ["role" => "model", "parts" => [["text" => "Understood 💜 I'm Luna, ready to listen with care."]]];

// Add conversation history
foreach ($histResult as $msg) {
    $geminiRole = ($msg["role"] === "user") ? "user" : "model";
    $contents[] = ["role" => $geminiRole, "parts" => [["text" => $msg["message"]]]];
}

// Add current user message
$contents[] = ["role" => "user", "parts" => [["text" => $userMessage]]];

// ── Call Gemini API ───────────────────────────────────────────────────────
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

$postData = ["contents" => $contents];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS     => json_encode($postData),
    CURLOPT_TIMEOUT        => 20,
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(["reply" => "Connection error 💜 Please try again."]);
    curl_close($ch); exit;
}
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $errData = json_decode($response, true);
    $errMsg  = $errData["error"]["message"] ?? "";
    if (stripos($errMsg, "quota") !== false) {
        echo json_encode(["reply" => "I need a short break 💜 Please try again in a moment!"]);
    } else {
        echo json_encode(["reply" => "Something went wrong on my end 💜 Please try again."]);
    }
    exit;
}

$result = json_decode($response, true);
$reply  = $result["candidates"][0]["content"]["parts"][0]["text"] ?? "I'm here for you 💜";

// ── Save both messages to DB ──────────────────────────────────────────────
$stmt = $conn->prepare("INSERT INTO chat_messages (user_id, conversation_id, role, message) VALUES (?, ?, ?, ?)");
$role = "user";
$stmt->bind_param("iiss", $userId, $conversationId, $role, $userMessage);
$stmt->execute();
$role = "bot";
$stmt->bind_param("iiss", $userId, $conversationId, $role, $reply);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(["reply" => $reply]);
?>