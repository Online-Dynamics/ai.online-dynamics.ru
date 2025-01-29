<?php
header('Content-Type: application/json');

$config = require_once __DIR__ . '/../config/telegram.php';
define('TELEGRAM_BOT_TOKEN', $config['bot_token']);
define('TELEGRAM_CHAT_ID', $config['chat_id']);


// Get and validate phone number
$data = json_decode(file_get_contents("php://input"), true);
$phone = $data['phone'] ?? '';

// Basic validation
if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Введите номер телефона']);
    exit;
}

// Format message for Telegram
$message = "🔔 New call request!\n";
$message .= "📱 Phone: {$phone}\n";
$message .= "🕐 Time: " . date('Y-m-d H:i:s') . "\n";
$message .= "🌐 Site: " . $_SERVER['HTTP_HOST'];

// Send to Telegram
$telegramUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
$telegramData = [
    'chat_id' => TELEGRAM_CHAT_ID,
    'text' => $message,
    'parse_mode' => 'HTML'
];

// Make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $telegramData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);


// For now, let's simulate successful processing
echo json_encode([
    'success' => true,
    'message' => 'Спасибо. Мы вам свяжемся в ближайшее время'
]);
