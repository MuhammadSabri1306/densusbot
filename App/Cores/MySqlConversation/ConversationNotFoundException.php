<?php
namespace App\Cores\MySqlConversation;

class ConversationNotFoundException extends \Exception
{
    function __construct($message = '', $code = 0) {
        parent::__construct($message);
        $this->code = $code;
    }
}