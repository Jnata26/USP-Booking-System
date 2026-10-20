<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// 1. Get user input
$data = json_decode(file_get_contents('php://input'), true);
$user_message = trim($data['message'] ?? '');

if (empty($user_message)) {
    echo json_encode(['reply' => 'Please type a message.']);
    exit;
}

// 2. Fetch current live inventory from the Database
$stmt = $pdo->query("SELECT name, category, qty FROM equipment");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$inventory_context = "CURRENT LIVE INVENTORY:\n";
foreach ($items as $item) {
    $inventory_context .= "- {$item['name']} ({$item['category']}): {$item['qty']} available\n";
}

// 3. Define the Strict System Instruction (Context & Rules)
$system_prompt = "You are the official AI Assistant for the University of the South Pacific (USP) Equipment Booking System. 
Your ONLY job is to help students with equipment booking. If a user asks about topics unrelated to the USP Equipment Booking System or general knowledge, politely decline to answer and remind them of your purpose.

RULES & POLICIES:
- Duration: Maximum booking duration is strictly 2 hours per session.
- Returns: Items must be returned 5 minutes before the time expires. A 5-minute grace period applies.
- Late Penalties: Returning an item late results in a $5.00 fine and places an automatic hold on the account.
- Cancellations: Must be made at least 1 hour prior to the scheduled time, or a $5.00 late cancellation fee will apply.
- Account Holds: Outstanding fines must be paid at the physical equipment counter to lift the hold.
- Laptops: Come with a mouse and headphones upon request.
- Cameras: Include a standard lens, battery pack, and strap upon request.
- Support Email: equipment.support@usp.ac.fj

$inventory_context

Keep your answers friendly, concise, and helpful. Use bullet points for readability when listing items or rules.";

// 4. Call the Gemini API
$api_key = 'AQ.Ab8RN6LxdB60wiyxTTw4CDXrKvK_kyB4afIx_QSUEKQN4L8xlQ'; // Gemini API Key
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-:generateContent?key=' . $api_key;

$payload = [
    "system_instruction" => [
        "parts" => [
            ["text" => $system_prompt]
        ]
    ],
    "contents" => [
        [
            "role" => "user",
            "parts" => [
                ["text" => $user_message]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Bypasses SSL verification for local XAMPP servers
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch); // Capture network errors just in case
curl_close($ch);

// 5. Return the AI's response to the frontend
if ($http_code == 200) {
    $result = json_decode($response, true);
    $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't process that response.";
    echo json_encode(['reply' => $reply]);
} else {
    if ($curl_error) {
        echo json_encode(['reply' => "Local Network Error: " . $curl_error]);
    } else {
        $error_data = json_decode($response, true);
        $google_error = $error_data['error']['message'] ?? "Unknown API Error. HTTP Code: " . $http_code;
        echo json_encode(['reply' => "Google API Rejected Request: " . $google_error]);
    }
}
exit;
?>