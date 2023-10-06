<?php
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/botconfig.php';
$config = getBotConfig();

try {

    $telegram = new Longman\TelegramBot\Telegram($config['api_key'], $config['bot_username']);
    $result = $telegram->setWebhook($config['webhook']['url']);

    if($result->isOk()) {
        dd_json($result);
    }

} catch (Longman\TelegramBot\Exception\TelegramException $err) {
    echo $err->getMessage();
    dd($err);
} catch (\Exception $err) {
    echo $err->getMessage();
    dd($err);
}