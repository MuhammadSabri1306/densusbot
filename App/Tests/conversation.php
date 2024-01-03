<?php
use App\Cores\MySqlConversation\Conversation;
use App\Cores\MySqlConversation\ConversationNotFoundException;
use App\Controllers\Bot\UserController;

$chatId = 1931357638;
$userId = 1931357638;
$conversationName = UserController::getConversationName('regist');
$conversation = Conversation::callOrCreate(UserController::$convRegistName, $chatId, $userId);

dd($conversation);