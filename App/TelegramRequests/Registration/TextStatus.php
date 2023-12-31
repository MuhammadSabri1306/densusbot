<?php
namespace App\TelegramRequests\Registration;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class TextStatus extends TelegramRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        $botUsername = env('BOT_USERNAME', null);
        $text = TelegramText::create('Anda telah terdaftar sebagai pengguna ');
        if($botUsername) {
            $text->addMentionByUsername($botUsername);
        } else {
            $text->addBold('Densus Telegram BOT');
        }
        $text->addText('.');
        return $text;
    }

    public function send(): ServerResponse
    {
        $response = Request::sendMessage($this->params->build());
        return $this->catchFailed($response);
    }
}