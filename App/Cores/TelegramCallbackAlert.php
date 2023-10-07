<?php
namespace App\Cores;

class TelegramCallbackAlert
{
    public $text;
    public $cacheTime;

    public function __construct(string $text, int $cacheTime = 5)
    {
        $this->text = $text;
        $this->cacheTime = $cacheTime;
    }

    public function get()
    {
        return [
            'text' => $this->text,
            'show_alert' => (bool) 1,
            'cache_time' => $this->cacheTime,
        ];
    }
}
