<?php
use App\Models\TelgUser;

$chatId = env('TELEGRAM_DEV_CHAT_ID', 12345);
$telgUser = TelgUser::findByChatId($chatId);