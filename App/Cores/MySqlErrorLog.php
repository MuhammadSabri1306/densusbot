<?php
namespace App\Cores;

use App\Cores\DB;

class MySqlErrorLog
{
    public $id;
    public $exists;
    public $type;
    public $message;
    public $tracedData;

    private $db;
    private static $tableName = 'error_log';

    public function __construct($type = 'server', $tracedData = null)
    {
        $this->db = new DB();
        $this->exists = false;
        $this->type = $type;
        if($tracedData) $this->setData($tracedData);
    }

    public function __get($key)
    {
        if(!is_array($this->tracedData)) {
            return null;
        }

        return $this->tracedData[$key] ?? null;
    }

    public function __set($key, $value)
    {
        if(!is_array($this->tracedData)) {
            $this->tracedData = [];
        }

        $this->tracedData[$key] = $value;
        $this->exists = true;
    }

    public function setData($tracedData)
    {
        if(!is_array($tracedData)) {
            $tracedData = (array) $tracedData;
        }
        $this->tracedData = $tracedData;
        $this->exists = true;
    }

    public function catchBasicError($err)
    {
        $this->message = $err->getMessage();
        $traceList = $err->getTrace();

        array_unshift($traceList, [
            'file' => $err->getFile() ?? null,
            'line' => $err->getLine() ?? null
        ]);
        
        $this->tracedData = [ 'trace_list' => $traceList ];
        $this->exists = true;
        
    }

    public function get()
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'traced_data' => $this->tracedData,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    public function record()
    {
        $data = $this->get();
        if(!is_null($data['traced_data'])) {
            $data['traced_data'] = json_encode($data['traced_data']);
        }

        $this->db->insert(MySqlErrorLog::$tableName, $data);
        $id = $this->db->insertId();
        if($id) {
            $this->id = $id;
        }
    }

    public static function getAllLogs($callQuery = null)
    {
        $tableName = MySqlErrorLog::$tableName;
        $db = new DB();
        
        $rows = is_callable($callQuery) ? $callQuery($db, $tableName) : $db->query("SELECT * FROM $tableName");
        return array_map(function($item) {

            $item['traced_data'] = json_decode($item['traced_data'], true);
            return $item;

        }, $rows);
    }
}