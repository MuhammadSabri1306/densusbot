<?php
namespace App\Cores;

class Collection
{
    private $data;

    public function __construct($initData = [])
    {
        if(!is_array($initData)) {
            $initData = [];
        }
        
        if(!empty($initData) && array_values($initData) === $initData) {
            $initData = [];
        }

        $this->data = $initData;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __get($key)
    {
        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return null;
    }

    public function remove($key)
    {
        if(array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
    }

    public function get(array $keys = [])
    {
        if(empty($keys)) {
            return $this->data;
        }

        $result = [];
        foreach($keys as $key => $targetKey) {
            $srcKey = is_int($key) ? $targetKey : $key;
            // dd($key, $targetKey, $srcKey);
            if(array_key_exists($srcKey, $this->data)) {
                $result[$targetKey] = $this->data[$srcKey];
            } else {
                $result[$targetKey] = null;
            }
        }

        return $result;
    }

    public static function createList($initData = [])
    {
        if(!is_array($initData)) {
            $initData = [];
        }
        
        if(!empty($initData) && array_values($initData) !== $initData) {
            $initData = [];
        }

        $collections = array_map(function($item) {
            return new Collection($item);
        }, $initData);

        return $collections;
    }
}