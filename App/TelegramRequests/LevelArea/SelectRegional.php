<?php
namespace App\TelegramRequests\LevelArea;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class SelectRegional extends TelegramRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        return TelegramText::create('Silahkan pilih ')
            ->addBold('Regional')
            ->addText('.');
    }

    public function setInKeyboard(callable $callButton)
    {
        $inlineKeyboardData = array_map(function($regional) use ($callButton) {

            $item = $callButton([ 'text' => $regional['name'], 'callback_data' => null ], $regional);
            return [ $item ];

        }, $this->getData('regionals', []));
        
        $this->params->replyMarkup = new InlineKeyboard(...$inlineKeyboardData);
    }

    public function send(): ServerResponse
    {
        $response = Request::sendMessage($this->params->build());
        return $this->catchFailed($response);
    }
}