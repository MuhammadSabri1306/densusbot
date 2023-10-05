<?php
require __DIR__ . '/bootstrap.php';

$botToken = env('BOT_TOKEN', '');
$botUsername = env('BOT_USERNAME', '');

$useMysql = env('BOT_USE_MYSQL', false);
$mysqlPrefix = env('BOT_MYSQL_PREFIX', null);
$mysqlConfig = [
   'host'     => env('MYSQL_HOST', 'localhost'),
   'port'     => env('MYSQL_PORT', 3306),
   'user'     => env('MYSQL_USERNAME', 'root'),
   'password' => env('MYSQL_PASSWORD', ''),
   'database' => env('MYSQL_DATABASE', '')
];

try {

    $telegram = new Longman\TelegramBot\Telegram($botToken, $botUsername);

    if($useMysql) {
        if($mysqlPrefix) {
            $telegram->enableMySql($mysqlConfig, $mysqlPrefix.'_');
        } else {
            $telegram->enableMySql($mysqlConfig);
        }
    } else {
        $telegram->useGetUpdatesWithoutDatabase();
    }

    $telegram->handleGetUpdates();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $err->getMessage();
    dd($err);
}