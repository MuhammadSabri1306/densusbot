<?php
error_reporting(E_ALL);
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/botconfig.php';
$config = getBotConfig();

$log = new App\Cores\MySqlErrorLog('telegram-hook');
$useMysql = env('BOT_USE_MYSQL', false);
$mysqlPrefix = env('BOT_MYSQL_PREFIX', null);

try {

    $telegram = new Longman\TelegramBot\Telegram($config['api_key'], $config['bot_username']);
    $telegram->enableAdmins($config['admins']);
    $telegram->addCommandsPaths($config['commands']['paths']);
    $telegram->enableLimiter($config['limiter']);

    if($useMysql) {
        if($mysqlPrefix) {
            $telegram->enableMySql($config['mysql'], $mysqlPrefix.'_');
        } else {
            $telegram->enableMySql($config['mysql']);
        }
    } else {
        $telegram->useGetUpdatesWithoutDatabase();
    }

    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $err) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();

    $log->catchBasicError($err);
    $log->record();

} catch(\Error $err) {

    $log->catchBasicError($err);
    $log->record();
    
} catch(\Exception $err) {
    
    $log->catchBasicError($err);
    $log->record();
    
}