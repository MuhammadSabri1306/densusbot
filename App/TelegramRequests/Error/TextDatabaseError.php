<?php
namespace App\TelegramRequests\Error;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class TextDatabaseError extends TelegramRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        return TelegramText::create()
            ->addBold('Gagal Koneksi')->newLine()
            ->addText('Terdapat masalah saat menghubungi database. Silahkan ulangi beberapa saat lagi.');
    }

    public function send(): ServerResponse
    {
        return Request::sendMessage($this->params->build());
    }
}