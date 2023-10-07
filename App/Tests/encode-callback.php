<?php
use App\Controllers\BotController;
use App\Controllers\Bot\UserController;

$request = BotController::request('Registration/SelectTouApproval');
$request->params->chatId = 222;
$request->setInKeyboard(function($inKeyboard) {
    $inKeyboard['approve']['callback_data'] = UserController::encodeCallback('onApprove', ['title' => 'Setuju']);
    $inKeyboard['reject']['callback_data'] = UserController::encodeCallback('onApprove', ['title' => 'Batalkan']);
    return $inKeyboard;
});

dd(UserController::encodeCallback('onApprove', ['title' => 'Setuju']), $request->params->build());