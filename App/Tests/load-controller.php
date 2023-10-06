<?php
use App\Controllers\BotController;

class Test {
    public function execute()
    {
        return BotController::handleCommand($this, 'UserController');
    }
    public function replyToChat($text)
    {
        dd($text);
    }
}

$test = new Test();
$test->execute();