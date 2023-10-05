<?php
require __DIR__ . '/bootstrap.php';

$botToken = env('BOT_TOKEN', '');
$botUsername = env('BOT_USERNAME', '');
$commandPath = env('BOT_COMMANDS_PATH', '');

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($botToken, $botUsername);

    // Add custom commands path
    $telegram->addCommandsPath([ $commandPath ]);

    // Handle telegram webhook request
    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();
}