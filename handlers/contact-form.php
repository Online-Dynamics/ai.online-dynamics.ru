<?php
header('Content-Type: application/json');

// Load Telegram config
$config = require_once __DIR__ . '/../config/telegram.php';
define('TELEGRAM_BOT_TOKEN', $config['bot_token']);
define('TELEGRAM_CHAT_ID', $config['chat_id']);

// Get and validate inputs
$data = json_decode(file_get_contents("php://input"), true);
$phone = $data['phone'] ?? '';
$name = $data['name'] ?? '';
$contact = $data['contact'] ?? '';
$message = $data['message'] ?? '';
$botCheck = $data['botCheck'] ?? '';

// Validation
if (empty($name) || empty($contact) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

// Bot protection check
if ($botCheck !== '4') {
    echo json_encode([
        'success' => false,
        'message' => 'Неверный ответ на проверочный вопрос'
    ]);
    exit;
}

// Format message
$telegramMessage = "💌 Новое сообщение с сайта!\n\n";
$telegramMessage .= "👤 Имя: {$name}\n";
$telegramMessage .= "📞 Контакт: {$contact}\n";
$telegramMessage .= "💬 Сообщение: {$message}\n";
$telegramMessage .= "🕐 Время: " . date('Y-m-d H:i:s');

// Send to Telegram
$telegramUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
$telegramData = [
    'chat_id' => TELEGRAM_CHAT_ID,
    'text' => $telegramMessage,
    'parse_mode' => 'HTML'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $telegramData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo json_encode([
    'success' => true,
    'message' => 'Спасибо! Мы свяжемся с вами в ближайшее время'
]);
