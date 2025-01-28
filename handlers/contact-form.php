<?php
header('Content-Type: application/json');

// Load Telegram config
$config = require_once __DIR__ . '/../config/telegram.php';
define('TELEGRAM_BOT_TOKEN', $config['bot_token']);
define('TELEGRAM_CHAT_ID', $config['chat_id']);

// Get and validate inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
$botCheck = filter_input(INPUT_POST, 'botCheck', FILTER_SANITIZE_NUMBER_INT);

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
