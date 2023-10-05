<?php
namespace App\Cores;

use Longman\TelegramBot\Entities\ServerResponse;
use App\Cores\TelegramParams;

abstract class TelegramRequest
{
    protected $data = [];
    public $params;

    public function __construct()
    {
        $this->params = new TelegramParams();
    }

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getData($key = null, $defaultValue = null)
    {
        if(is_null($key)) {
            return $this->data;
        }

        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $defaultValue;
    }

    abstract public function send(): ServerResponse;
}