<?php
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/botconfig.php';
$config = getBotConfig();

$log = new App\Cores\MySqlErrorLog('telegram-hook');

try {

    $telegram = new Longman\TelegramBot\Telegram($config['api_key'], $config['bot_username']);
    $telegram->enableAdmins($config['admins']);
    $telegram->addCommandsPaths($config['commands']['paths']);
    $telegram->enableLimiter($config['limiter']);
    if(env('BOT_USE_MYSQL', false)) {
        $telegram->enableMySql($config['mysql']);
    }

    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $err) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();

    $log->message = $err->getMessage();
    $log->catch($err);
    $log->record();

} catch(\Error $err) {

    $log->message = $err->getMessage();
    $log->catch($err);
    $log->record();
    
} catch(\Exception $err) {
    
    $log->message = $err->getMessage();
    $log->catch($err);
    $log->record();
    
}