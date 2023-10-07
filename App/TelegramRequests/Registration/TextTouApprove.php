<?php
namespace App\TelegramRequests\Registration;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class TextTouApprove extends TelegramRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        return TelegramText::create('Registrasi dilanjutkan, terima kasih.');
    }

    public function send(): ServerResponse
    {
        $response = Request::sendMessage($this->params->build());
        return $this->catchFailed($response);
    }
}