<?php

use App\Cores\Collection;

$registData = new Collection();

$registData->level = 'witel';
$registData->regionalId = '7';
$registData->witelId = '53';
$registData->replyTo = 1931357638;

$registData->chatId = 1931357638;
$registData->userId = 1931357638;
$registData->type = 'private';
if($registData->type != 'private') {
    $registData->title = 'Grup Test';
}
if($registData->type != 'group') {
    $registData->username = 'Sabri_m13';
}
if($registData->type == 'private') {
    $registData->firstName = 'Muhammad';
    $registData->lastName = 'Sabri';
}

$data = [
    'chat_id' => $registData->chatId,
    'type' => $registData->type,
    'telegram_data' => $registData->get([
        'chatId' => 'chat_id',
        'userId' => 'user_id',
        'type',
        'title',
        'username',
        'firstName' => 'first_name',
        'lastName' => 'last_name'
    ]),
    'level' => $registData->level,
    'regional_id' => $registData->regionalId,
    'witel_id' => $registData->witelId,
];

dd($data);