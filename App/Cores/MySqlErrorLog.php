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
    private $tableName = 'error_log';

    public function __construct($type = 'server', $tracedData = null)
    {
        $this->db = new DB();
        $this->exists = false;
        $this->type = $type;
        if($tracedData) $this->catch($tracedData);
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

    public function catch($tracedData)
    {
        if(!is_array($tracedData)) {
            $tracedData = (array) $tracedData;
        }
        $this->tracedData = $tracedData;
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

        $this->db->insert($this->tableName, $data);
        $id = $this->db->insertId();
        if($id) {
            $this->id = $id;
        }
    }
}