<?php
namespace App\TelegramRequests\Action;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramRequest;
use App\Cores\TelegramText;

class TextAnswerSelect extends TelegramRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->params->parseMode = 'markdown';
        $this->params->text = $this->text()->get();
    }

    public function text()
    {
        $questionText = $this->getData('question', '');
        $answerText = $this->getData('answer', '');
        return TelegramText::create($questionText)->newLine(2)
            ->addBold('=> ')->addText($answerText);
    }

    public function setQuestionText($questionText)
    {
        $this->setData('question', $questionText);
        $this->params->text = $this->text()->get();
    }

    public function setAnswerText($answerText)
    {
        $this->setData('answer', $answerText);
        $this->params->text = $this->text()->get();
    }

    public function send(): ServerResponse
    {
        $response = Request::editMessageText($this->params->build());
        return $this->catchFailed($response);
    }
}