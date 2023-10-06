<?php
namespace App\Controllers;

use Longman\TelegramBot\Request;
use App\Cores\TelegramParams;
// use App\Cores\Controller;
use App\Cores\MySqlErrorLog;

// class BotController extends Controller
class BotController
{
    public static $command;

    public static function handleCommand($command, $className)
    {
        try {
            
            $controller = 'App\\Controllers\\Bot\\'.$className;
            $filePath = __DIR__."/../Controllers/Bot/$className.php";
    
            require_once $filePath;
            $controller::$command = $command;
            return $controller::main();

        } catch(\Error $err) {

            $log = new MySqlErrorLog('server', $err);
            $log->message = $err->getMessage();
            $log->record();
            return BotController::main();

        }
    }

    protected static function main()
    {
        $controller = static::class;
        $controllerArr = explode('\\', $controller);
        $controllerName = end($controllerArr);
        return static::$command->replyToChat("Uncatched error in controller:$controllerName");
    }

    public static function sendDebugMessage($data, array $config = [])
    {
        global $appConfig;

        $chatId = isset($config['chatId']) ? $config['chatId'] : env('DEV_TEST_CHAT_ID', '');
        $isCode = isset($config['isCode']) ? $config['isCode'] : true;
        $toJson = isset($config['toJson']) ? $config['toJson'] : true;

        $reqData = New TelegramParams();
        $reqData->parseMode = 'markdown';
        $reqData->chatId = $chatId;

        if($toJson) {
            $data = json_encode($data, JSON_INVALID_UTF8_IGNORE);
        }
        
        if($isCode) {
            $reqData->text = '```'.PHP_EOL.$data.'```';
        } else {
            $reqData->text = $data;
        }
        return Request::sendMessage($reqData->build());
    }

    public static function request(string $classPath, array $args = [])
    {
        $classPathArr = explode('/', $classPath);
        $className = 'App\\TelegramRequests\\' . implode('\\', $classPathArr);
        $filePath = __DIR__."/../TelegramRequests/$classPath.php";

        require_once $filePath;
        return empty($args) ? new $className() : new $className(...$args);
    }
}