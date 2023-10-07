<?php
namespace App\Controllers\Bot;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;

use App\Controllers\BotController;
use App\Models\TelgUser;

class UserController extends BotController
{
    public static $callbackKey = 'user';

    public static function main()
    {
        $message = UserController::$command->getMessage();
        $chatType = $message->getChat()->getType();
        $chatId = $message->getChat()->getId();
        try {
    
            $telgUser = TelgUser::findByChatId($chatId);
            if($telgUser) {
                
                $request = BotController::request('Registration/TextStatus');
                $request->params->chatId = $chatId;
                return $request->send();
                
            }
    
            $request = BotController::request('Registration/SelectTouApproval');
            $request->params->chatId = $chatId;
            $request->setInKeyboard(function($inKeyboard) {
                $inKeyboard['approve']['callback_data'] = UserController::encodeCallback('onTouApprove', ['title' => 'Setuju']);
                $inKeyboard['reject']['callback_data'] = UserController::encodeCallback('onTouReject', ['title' => 'Batal']);
                return $inKeyboard;
            });

            $response = $request->send();
            BotController::sendDebugMessage($response);
            return $response;
            
        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }

    }

    public static function onTouApprove($option)
    {
        $messageId = UserController::$callback['message']->getMessageId();
        $chatId = UserController::$callback['message']->getChat()->getId();
        try {

            $srcRequest = BotController::request('Registration/SelectTouApproval');
            
            $request = BotController::request('Action/TextAnswerSelect');
            $request->params->chatId = $chatId;
            $request->params->messageId = $messageId;
            $request->setQuestionText($srcRequest->text->get());
            $request->setAnswerText($option->title);
            return $request->send();

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }
    }

    public static function onTouReject($callbackValue)
    {
        $messageId = UserController::$callback['message']->getMessageId();
        $chatId = UserController::$callback['message']->getChat()->getId();
        try {

            $srcRequest = UserController::request('Registration/SelectTouApproval');
            
            $request = UserController::request('Action/TextAnswerSelect');
            $request->params->chatId = $chatId;
            $request->params->messageId = $messageId;
            $request->setQuestionText($srcRequest->text->get());
            $request->setAnswerText($option->title);
            $request->send();

            return UserController::alert('Registrasi dibatalkan. Terima kasih.');

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        }
    }
}