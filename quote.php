<?php


header("Content-Type: application/json");

$apiKey = "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";
$url = "https://api.groq.com/openai/v1/chat/completions"; 



$postData = [
    "model" => "llama-3.3-70b-versatile",
    "messages" => [
        [
            "role" => "user",
            "content" => "Generate one short, deeply resonant mental health affirmation. 

CRITICAL CONSTRAINTS:
1. Do NOT use cliché or overused AI phrases such as 'You are strong,' 'You are enough,' 'Keep pushing,' 'You've got this,' or 'Storms don't last forever.' Instead, craft something that feels fresh, original, and truly heartfelt.
2. Avoid generic motivational speaker language.
3. Focus on themes of self-compassion, quiet resilience, pacing oneself, or embracing imperfection.
4. Tone: Poetic, grounded, gentle, and deeply authentic. 
5. Length: 1 to 2 sentences max.  Return ONLY a JSON array like this, no markdown, no backticks, nothing else: [{\"quote\": \"affirmation here\", \"author\": \"Soul Therapy\"}]"
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
    $errData = json_decode($response, true);
    echo json_encode(["error" => "HTTP $httpCode", "details" => $errData]);
    exit;
}
if ($httpCode !== 200) {
    echo json_encode([["quote" => "I am resilient, strong, and completely capable of navigating this day.", "author" => "Soul Therapy"]]);
    exit;
}

$result  = json_decode($response, true);
$aiText  = $result["choices"][0]["message"]["content"] ?? "";
$aiText  = preg_replace('/```json|```/i', '', $aiText);
$aiText  = trim($aiText);
$decoded = json_decode($aiText, true);

if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded[0]["quote"])) {
    echo json_encode($decoded);
    exit;
}

// Fallback pool — bas aikkkkk
$fallbacks = [
    ["quote" => "I am resilient, strong, and completely capable of navigating this day.",           "author" => "Soul Therapy"],
//     ["quote" => "My peace of mind is worth more than any situation outside of my control.",         "author" => "Soul Therapy"],
//     ["quote" => "I choose to be profoundly kind to myself and honor my emotions.",                  "author" => "Soul Therapy"],
//     ["quote" => "I am growing and healing at a pace that is completely safe for me.",               "author" => "Soul Therapy"],
//     ["quote" => "My feelings are valid, and I give myself the time and space I need.",              "author" => "Soul Therapy"],
//     ["quote" => "I deserve rest, joy, and all the good things life has to offer.",                  "author" => "Soul Therapy"],
//     ["quote" => "I am not my anxiety. I am the calm beneath the storm.",                            "author" => "Soul Therapy"],
//     ["quote" => "My mind is becoming clearer and my heart is becoming lighter every single day.",   "author" => "Soul Therapy"],
 ];


echo json_encode([$fallbacks[array_rand($fallbacks)]]); 
exit;