<?php
namespace App\TelegramRequests\Error;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class TextLoggedError extends TelegramRequest
{
    public function __construct($errorId = null)
    {
        parent::__construct();
        if(!is_null($errorId)) $this->setData('errorId', $errorId);
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        $errorId = $this->getData('errorId', null);
        return TelegramText::create('An error was logged with ')
            ->addBold('id:'.$errorId)
            ->addText('. See details ')
            ->addUrl(publicUrl("/App/?page=error-log&id=$errorId"), 'here')
            ->addText('.');
    }

    public function setErrorId($errorId)
    {
        $this->setData('errorId', $errorId);
        $this->params->text = $this->text()->get();
    }

    public function send(): ServerResponse
    {
        return Request::sendMessage($this->params->build());
    }
}