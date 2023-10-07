<?php
namespace App\Cores;

class TelegramCallbackData
{
    private $callbackKey;
    public $handlerName;
    public $value;
    public $title;

    public function __construct($callbackKey)
    {
        $this->callbackKey = $callbackKey;
    }

    public function isCallbackOf($callbackKey)
    {
        return $this->callbackKey === $callbackKey;
    }

    public static function encodeValue(TelegramCallbackData $callbackData)
    {
        $encodedData = [];
        if($callbackData->title) array_push($encodedData, "[t=$callbackData->title]");
        if($callbackData->value) array_push($encodedData, "[v=$callbackData->value]");

        $encodedDataStr = implode('&', $encodedData);
        return "$callbackData->callbackKey.$callbackData->handlerName.$encodedDataStr";
    }

    public static function decodeValue($encodedCallbackData)
    {
        if(!is_string($encodedCallbackData)) {
            return null;
        }

        $node1 = strpos($encodedCallbackData, '.');
        $node2 = strpos($encodedCallbackData, '.', $node1 + 1);
        if($node1 === false || $node2 === false) {
            return null;
        }

        $callbackKey = substr($encodedCallbackData, 0, $node1);
        $callbackData = new TelegramCallbackData($callbackKey);
        $callbackData->handlerName = substr($encodedCallbackData, $node1 + 1, $node2 - $node1 - 1);

        $pattern = '/\[t=([^\]]+)\]/';
        if(preg_match($pattern, $encodedCallbackData, $matches)) {
            $callbackData->title = $matches[1];
        }

        $pattern = '/\[v=([^\]]+)\]/';
        if(preg_match($pattern, $encodedCallbackData, $matches)) {
            $callbackData->value = $matches[1];
        }

        return $callbackData;
    }
}