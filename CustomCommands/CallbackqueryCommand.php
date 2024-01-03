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
        BotController::$callback = (object) [
            'from' => $callbackQuery->getFrom(),
            'message' => $callbackQuery->getMessage()
        ];

        $response = BotController::catchCallback(
            $callbackData,
            UserController::class
        );
        
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

/*

=====> BotController::$callback->message
{
  "message_id": 45,
  "from": {
    "id": 6503513366,
    "is_bot": true,
    "first_name": "GEPEE",
    "username": "densusreport_bot"
  },
  "chat": {
    "id": 1931357638,
    "first_name": "Muhammad",
    "last_name": "Sabri",
    "username": "Sabri_m13",
    "type": "private"
  },
  "date": 1696711395,
  "text": "Dengan memilih Setuju anda akan menerima Alert.",
  "entities": [
    {
      "offset": 15,
      "length": 6,
      "type": "bold"
    }
  ],
  "reply_markup": {
    "inline_keyboard": [
      [
        {
          "text": "👍 Setuju",
          "callback_data": "user.onTouApprove.[t=Setuju]"
        },
        {
          "text": "❌ Batal",
          "callback_data": "user.onTouReject.[t=Batal]"
        }
      ]
    ]
  }
}

=====> BotController::$callback->from
{
  "id": 1931357638,
  "is_bot": false,
  "first_name": "Muhammad",
  "last_name": "Sabri",
  "username": "Sabri_m13",
  "language_code": "en"
}
*/