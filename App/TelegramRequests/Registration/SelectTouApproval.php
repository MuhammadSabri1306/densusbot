<?php
namespace App\TelegramRequests\Registration;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class SelectTouApproval extends TelegramRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        return TelegramText::create('Dengan memilih ')
            ->addBold('Setuju')
            ->addText(' anda akan menerima Report Konsumsi Listrik Harian dari data ')
            ->addBold('Densus')
            ->addText('. Lanjutkan?');
    }

    public function setInKeyboard(callable $callButton)
    {
        $inKeyboardItem = $callButton([
            'approve' => ['text' => '👍 Setuju', 'callback_data' => null],
            'reject' => ['text' => '❌ Batal', 'callback_data' => null]
        ]);

        $inlineKeyboardData = [ $inKeyboardItem['approve'], $inKeyboardItem['reject'] ];
        $this->params->replyMarkup = new InlineKeyboard($inlineKeyboardData);
    }

    public function send(): ServerResponse
    {
        $response = Request::sendMessage($this->params->build());
        return $this->catchFailed($response);
    }
}