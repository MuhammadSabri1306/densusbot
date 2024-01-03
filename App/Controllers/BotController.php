<?php
namespace App\Controllers;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Cores\TelegramParams;
use App\Cores\TelegramText;
use App\Cores\Controller;
use App\Cores\MySqlErrorLog;
use App\Cores\TelegramCallbackAlert;
use App\Cores\TelegramCallbackData;

class BotController extends Controller
{
    public static $command;
    public static $callback;
    public static $callbackKey = '';

    public static function handleCommand($command, $className)
    {
        try {
            
            $controller = 'App\\Controllers\\Bot\\'.$className;
            $filePath = __DIR__."/../Controllers/Bot/$className.php";
    
            require_once $filePath;
            $controller::$command = $command;
            $response = $controller::main();

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);
            $response = BotController::main();

        } finally {

            if($response instanceof ServerResponse) {
                return $response;
            }
            return Request::emptyResponse();

        }
    }

    public static function catchCallback($encodedCallbackData, ...$controllers)
    {
        try {

            $callbackValue = TelegramCallbackData::decodeValue($encodedCallbackData);
            if(!$callbackValue) return null;
    
            $callbackHandler = $callbackValue->handlerName;
            foreach($controllers as $controller) {
    
                $isKeyMatch = $callbackValue->isCallbackOf($controller::$callbackKey);
                if($isKeyMatch && method_exists($controller, $callbackHandler)) {
                    return $controller::$callbackHandler($callbackValue);
                }
    
            }
    
            return null;

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);
            return null;

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
        $chatId = isset($config['chatId']) ? $config['chatId'] : env('DEV_TEST_CHAT_ID', '');
        $isCode = isset($config['isCode']) ? $config['isCode'] : true;
        $toJson = isset($config['toJson']) ? $config['toJson'] : true;

        $reqParams = New TelegramParams();
        $reqParams->parseMode = 'markdown';
        $reqParams->chatId = $chatId;

        if($toJson) {
            $data = json_encode($data, JSON_INVALID_UTF8_IGNORE);
        }
        
        if($isCode) {
            $reqParams->text = '```'.PHP_EOL.$data.'```';
        } else {
            $reqParams->text = $data;
        }
        return Request::sendMessage($reqParams->build());
    }

    public static function sendErrorLogMessage($errLog)
    {
        if(!$errLog instanceof MySqlErrorLog) {
            return null;
        }

        $request = BotController::request('Error/TextLoggedError', [$errLog->id]);
        $request->params->chatId = env('DEV_TEST_CHAT_ID', '');
        $request->send();
    }

    public static function request(string $classPath, array $args = [])
    {
        $classPathArr = explode('/', $classPath);
        $className = 'App\\TelegramRequests\\' . implode('\\', $classPathArr);
        $filePath = __DIR__."/../TelegramRequests/$classPath.php";

        require_once $filePath;
        return empty($args) ? new $className() : new $className(...$args);
    }

    public static function alert(string $text, int $cacheTime = 5)
    {
        return new TelegramCallbackAlert($text, $cacheTime);
    }

    public static function encodeCallback($handlerName, array $option = [])
    {
        $callback = new TelegramCallbackData(static::$callbackKey);
        $callback->handlerName = $handlerName;

        if(isset($option['title'])) $callback->title = $option['title'];
        if(isset($option['value'])) $callback->value = $option['value'];

        return TelegramCallbackData::encodeValue($callback);
    }

    public static function encodeCallbacks($handlerName, array $options = [])
    {
        $callback = new TelegramCallbackData(static::$callbackKey);
        $callback->handlerName = $handlerName;

        $callbackDatas = [];
        foreach($options as $key => $option) {
            if(isset($option['title'])) $callback->title = $option['title'];
            if(isset($option['value'])) $callback->value = $option['value'];
            $callbackDatas[$key] = TelegramCallbackData::encodeValue($callback);
        }

        return $callbackDatas; 
    }

    protected static function conversations(): array
    {
        return [];
    }

    public static function getConversationName($key = null)
    {
        $list = static::conversations();
        return isset($list[$key]) ? $list[$key] : $list;
    }
}