<?php
header("Content-Type: application/json");

$apiKey = "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";
$url    = "https://api.x.ai/v1/chat/completions";

$postData = [
    "model" => "grok-3-mini",
    "messages" => [
        [
            "role" => "user",
            "content" => "Generate one short, beautiful mental health affirmation for someone feeling stressed or anxious. Start with I, me, or myself. Return ONLY a JSON array like this, no markdown, no backticks, nothing else: [{\"quote\": \"affirmation here\", \"author\": \"Soul Therapy\"}]"
        ]
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ["Content-Type: application/json", "Authorization: Bearer $apiKey"],
    CURLOPT_POSTFIELDS     => json_encode($postData),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 15,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $fallbacks = [
        ["quote" => "I am resilient, strong, and completely capable of navigating this day.", "author" => "Soul Therapy"],
        // ["quote" => "My peace of mind is worth more than any situation outside of my control.", "author" => "Soul Therapy"],
        // ["quote" => "I choose to be kind to myself and honor my emotions.", "author" => "Soul Therapy"],
        // ["quote" => "I am growing and healing at a pace that is safe for me.", "author" => "Soul Therapy"],
        // ["quote" => "I deserve rest, joy, and all the good things life has to offer.", "author" => "Soul Therapy"],
    ];
    echo json_encode([$fallbacks[array_rand($fallbacks)]]);
    exit;
}

$result = json_decode($response, true);
$aiText = $result["choices"][0]["message"]["content"] ?? "";
$aiText = preg_replace('/```json|```/i', '', $aiText);
$aiText = trim($aiText);

$decoded = json_decode($aiText, true);

if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded[0]["quote"])) {
    echo json_encode($decoded);
    exit;
}

// Fallback if parsing fails
$fallbacks = [
    ["quote" => "I am resilient, strong, and completely capable of navigating this day.", "author" => "Soul Therapy"],
    // ["quote" => "My peace of mind is worth more than any situation outside of my control.", "author" => "Soul Therapy"],
    // ["quote" => "I am not my anxiety. I am the calm beneath the storm.", "author" => "Soul Therapy"],
];
echo json_encode([$fallbacks[array_rand($fallbacks)]]);
exit;