<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramCallbackAlert;
use App\Controllers\BotController;
use App\Controllers\Bot\UserController;

class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Handle the callback query';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws \Exception
     */
    public function execute(): ServerResponse
    {
        $callbackQuery = $this->getCallbackQuery();
        $callbackData = $callbackQuery->getData();
        BotController::$callback = [
            'from' => $callbackQuery->getFrom(),
            'message' => $callbackQuery->getMessage()
        ];

        $response = BotController::catchCallback(
            $callbackData,
            UserController::class
        );

        BotController::sendDebugMessage($response);
        
        if($response instanceof TelegramCallbackAlert) {
            return $callbackQuery->answer($response->get());
        }

        if($response instanceof ServerResponse) {
            return $callbackQuery->answer();
        }

        return $callbackQuery->answer([
            'text'       => 'Content of the callback data: ' . $callback_data,
            'show_alert' => (bool) random_int(0, 1), // Randomly show (or not) as an alert.
            'cache_time' => 5,
        ]);
    }
}
