<?php
require __DIR__ . '/bootstrap.php';

$botToken = env('BOT_TOKEN', '');
$botUsername = env('BOT_USERNAME', '');

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($botToken, $botUsername);

    // Unset / delete the webhook
    $result = $telegram->deleteWebhook();
    if($result->isOk()) {
        dd_json($result);
    }

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $err->getMessage();
    dd($err);
}