<?php
require __DIR__ . '/bootstrap.php';

$botToken = env('BOT_TOKEN', '');
$botUsername = env('BOT_USERNAME', '');
$hookUrl = env('BOT_HOOK_URL', '');

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($botToken, $botUsername);

    // Set webhook
    $result = $telegram->setWebhook($hookUrl);
    if($result->isOk()) {
        dd_json($result);
    }
} catch (Longman\TelegramBot\Exception\TelegramException $err) {
    // log telegram errors
    echo $err->getMessage();
    dd($err);
}