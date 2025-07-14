<?php
$config = require __DIR__ . '/config.php';

$token   = $config['BOT_TOKEN'];
$chat_id = $config['CHAT_ID'];

// Получаем данные из $_POST, так как запрос с FormData
if (empty($_POST['name']) || empty($_POST['phone'])) {
    http_response_code(400);
    echo 'Invalid data';
    exit;
}

$title   = $_POST['title'] ?? 'Новая заявка с сайта';
$name    = htmlspecialchars($_POST['name']);
$phone   = htmlspecialchars($_POST['phone']);
$comment = htmlspecialchars($_POST['comment'] ?? '');

$message = "<b>{$title}</b>%0A";
$message .= "👤 Имя: {$name}%0A";
$message .= "📞 Телефон: {$phone}%0A";
if ($comment) $message .= "💬 Комментарий: {$comment}";

$url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=HTML&text={$message}";

$response = file_get_contents($url);

if ($response === false) {
    http_response_code(500);
    echo 'Ошибка при отправке';
} else {
    echo 'OK';
}
