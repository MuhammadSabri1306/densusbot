<?php
use App\Controllers\BotController;
use App\Controllers\Bot\UserController;

$encodedCallbackData = 'user.onTouApprove.[t=Setuju]';
BotController::catchCallback(
    $encodedCallbackData,
    UserController::class
);