<?php
namespace App\Controllers\Bot;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;

use App\Cores\MySqlErrorLog;
use App\Controllers\BotController;
use App\Models\TelgUser;

class UserController extends BotController
{
    public static function main()
    {
        $log = new MySqlErrorLog('server');
        try {

            $message = UserController::$command->getMessage();
            $chatType = $message->getChat()->getType();
            $chatId = $message->getChat()->getId();
    
            $telgUser = TelgUser::findByChatId($chatId);
            if($telgUser) {
                
                $request = BotController::request('Registration/TextStatus');
                $request->params->chatId = $chatId;
                return $request->send();
                
            }
    
            $request = BotController::request('Registration/SelectTouApproval');
            $request->params->chatId = $chatId;
            $request->setInKeyboard(function($inKeyboard) {
                $inKeyboard['approve']['callback_data'] = 'approve';
                $inKeyboard['reject']['callback_data'] = 'reject';
                return $inKeyboard;
            });

            $response = $request->send();
            BotController::sendDebugMessage($response);
            return $response;
            
        } catch(\Error $err) {

            $log->catch($err);
            $log->message = $err->getMessage();
            $log->record();
            BotController::sendDebugMessage("Error is thrown, id:$log->id");

        }

    }
}