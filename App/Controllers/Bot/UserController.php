<?php
namespace App\Controllers\Bot;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Message;

use App\Cores\Collection;
use App\Controllers\BotController;
use App\Models\TelgUser;
use App\Models\AlertKwhUser;
use App\Models\Regional;
use App\Models\Witel;

class UserController extends BotController
{
    public static $callbackKey = 'user';
    public static $convRegistName = 'user_regis';

    protected static function conversations(): array
    {
        return [ 'regist' => 'user.regist' ];
    }

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
                // BotController::sendDebugMessage($request->params->build());
                return $request->send();
                
            }
    
            $request = BotController::request('Registration/SelectTouApproval');
            $request->params->chatId = $chatId;
            $request->setInKeyboard(function($inKeyboard) {
                $inKeyboard['approve']['callback_data'] = UserController::encodeCallback('onTouApprove', ['title' => 'Setuju']);
                $inKeyboard['reject']['callback_data'] = UserController::encodeCallback('onTouReject', ['title' => 'Batal']);
                return $inKeyboard;
            });

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

    public static function onTouApprove($option)
    {
        $messageId = BotController::$callback->message->getMessageId();
        $chatId = BotController::$callback->message->getChat()->getId();
        try {

            $srcRequest = BotController::request('Registration/SelectTouApproval');
            
            $request = BotController::request('Action/TextAnswerSelect');
            $request->params->chatId = $chatId;
            $request->params->messageId = $messageId;
            $request->setQuestionText($srcRequest->text()->get());
            $request->setAnswerText($option->title);
            $request->send();

            // BotController::sendDebugMessage('TEST 1');
            $request = BotController::request('LevelArea/SelectRegional');
            $request->params->chatId = $chatId;
            $request->setData('regionals', Regional::getCodeOrdered());
            $request->setInKeyboard(function($inKeyboardItem, $regional) {
                $inKeyboardItem['callback_data'] = UserController::encodeCallback(
                    'onSelectRegional', [ 'value' => $regional['id'] ]
                );
                return $inKeyboardItem;
            });

            return $request->send();

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(\Exception $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }
    }

    public static function onTouReject($option)
    {
        $messageId = BotController::$callback->message->getMessageId();
        $chatId = BotController::$callback->message->getChat()->getId();
        try {

            $srcRequest = BotController::request('Registration/SelectTouApproval');
            
            $request = BotController::request('Action/TextAnswerSelect');
            $request->params->chatId = $chatId;
            $request->params->messageId = $messageId;
            $request->setQuestionText($srcRequest->text()->get());
            $request->setAnswerText($option->title);
            $request->send();

            return UserController::alert('Registrasi dibatalkan. Terima kasih.');

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(\Exception $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }
    }

    public static function onSelectRegional($option)
    {
        $messageId = BotController::$callback->message->getMessageId();
        $messageText = BotController::$callback->message->getText();
        $chatId = BotController::$callback->message->getChat()->getId();
        try {

            $regional = Regional::find($option->value);

            $request = BotController::request('Action/TextAnswerSelect');
            $request->params->chatId = $chatId;
            $request->params->messageId = $messageId;
            $request->setQuestionText($messageText);
            $request->setAnswerText($regional['name']);
            $request->send();

            $request = BotController::request('LevelArea/SelectWitel');
            $request->params->chatId = $chatId;
            $request->setData('witels', Witel::getCodeOrdered($regional['id']));
            $request->setInKeyboard(function($inKeyboardItem, $witel) {
                $inKeyboardItem['callback_data'] = UserController::encodeCallback(
                    'onSelectWitel', [ 'value' => $witel['id'] ]
                );
                return $inKeyboardItem;
            });

            return $request->send();

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(\Exception $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }
    }

    public static function onSelectWitel($option)
    {
        $userId = BotController::$callback->from->getId();
        $messageId = BotController::$callback->message->getMessageId();
        $messageText = BotController::$callback->message->getText();
        $chat = BotController::$callback->message->getChat();
        $chatId = $chat->getId();
        try {

            $witel = Witel::find($option->value);

            $request = BotController::request('Action/TextAnswerSelect');
            $request->params->chatId = $chatId;
            $request->params->messageId = $messageId;
            $request->setQuestionText($messageText);
            $request->setAnswerText($witel['name']);
            $response = $request->send();

            $registData = new Collection();

            $registData->level = 'witel';
            $registData->regionalId = $witel['regional_id'];
            $registData->witelId = $witel['id'];
            $registData->replyTo = $chatId;

            $registData->chatId = $chatId;
            $registData->userId = $userId;
            $registData->type = $chat->getType();
            if($registData->type != 'private') {
                $registData->groupDescr = $chat->getTitle();
            }
            if($registData->type != 'group') {
                $registData->username = $chat->getUsername();
            }
            if($registData->type == 'private') {
                $registData->firstName = $chat->getFirstName();
                $registData->lastName = $chat->getLastName();
            }

            return UserController::saveRegist($registData) ?? $response;

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(\Exception $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }
    }

    public static function saveRegist(Collection $registData)
    {
        try {

            $telgUser = TelgUser::create([
                'chat_id' => $registData->chatId,
                'type' => $registData->type,
                'telegram_data' => $registData->get([
                    'chatId' => 'chat_id',
                    'userId' => 'user_id',
                    'type',
                    'groupDescr' => 'group_descr',
                    'username',
                    'firstName' => 'first_name',
                    'lastName' => 'last_name'
                ]),
                'level' => $registData->level,
                'regional_id' => $registData->regionalId,
                'witel_id' => $registData->witelId,
            ]);

            if($telgUser && isset($telgUser['id'])) {

                AlertKwhUser::create([
                    'telg_user_id' => $telgUser['id'],
                    'send_alert' => 1
                ]);

            }

            if(!$telgUser) {

                $request = BotController::request('Error/TextBasicError', ['Gagal Registrasi']);
                $request->params->chatId = $registData->replyTo;
                return $request->send();

            }

            $request = BotController::request('Registration/TextUserSaved');
            $request->params->chatId = $registData->replyTo;
            return $request->send();

        } catch(\Error $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(\Exception $err) {

            $log = BotController::catchBasicError($err);
            BotController::sendErrorLogMessage($log);

        } catch(MeekroDBException $err) {
            
            $request = BotController::request('Error/TextDatabaseError');
            $request->params->chatId = $chatId;
            return $request->send();

        }
    }
}