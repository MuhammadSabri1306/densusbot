<?php
namespace App\TelegramRequests\Registration;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;
use App\Cores\Collection;

class TextUserSaved extends TelegramRequest
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
        $botUsernameText = !$botUsername ? TelegramText::create()->addBold('Densus Telegram BOT')->get()
            : TelegramText::create()->addMentionByUsername($botUsername)->get();
            
        return TelegramText::create()
            ->addBold('Pendaftaran Berhasil')->newLine()
            ->addText("Anda telah terdaftar sebagai pengguna $botUsernameText")
            ->addText('. Dengan ini anda akan menerima Report Konsumsi Listrik Daily dari data ')
            ->addBold('Densus')->addText(' setiap paginya.');
    }

    public function send(): ServerResponse
    {
        $response = Request::sendMessage($this->params->build());
        return $this->catchFailed($response);
    }
}