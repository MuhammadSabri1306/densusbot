<?php
namespace App\TelegramRequests\Error;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class TextBasicError extends TelegramRequest
{
    public function __construct($title = 'Gagal Koneksi')
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->setTitle($title);
    }

    public function text()
    {
        $title = $this->getData('title');
        return TelegramText::create()
            ->addBold($title)->newLine()
            ->addText('Terdapat masalah saat menghubungi server. Silahkan ulangi beberapa saat lagi.');
    }

    public function setTitle($title)
    {
        $this->setData('title', $title);
        $this->params->text = $this->text()->get();
    }

    public function send(): ServerResponse
    {
        return Request::sendMessage($this->params->build());
    }
}