<?php
namespace App\Cores\MySqlConversation;

use App\Cores\MySqlConversation\ConversationNotFoundException;
use App\Cores\DB;

class Conversation
{
    private $db;
    private $tableName = 'conversation';

    public $id;
    public $name;
    public $chatId;
    public $userId;
    public $step;
    private $state = [];
    private $status;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    public static function call($chatId, $userId)
    {
        $conversation = new Conversation();
        $row = $conversation->db->queryFirstRow(
            "SELECT * FROM $conversation->tableName WHERE chat_id=%i_chatid AND user_id=%i_userid",
            [ 'chatid' => $chatId, 'userid' => $userId ]
        );

        if(!$row) {
            throw new ConversationNotFoundException('Conversation not found');
        }

        $conversation->fillFromDb($row);
        return $conversation;
    }

    public static function callById($id)
    {
        $conversation = new Conversation();
        $row = $conversation->db->queryFirstRow("SELECT * FROM $conversation->tableName WHERE id=%i", $id);
        if(!$row) {
            throw new ConversationNotFoundException('Conversation not found');
        }

        $conversation->fillFromDb($row);
        return $conversation;
    }

    public static function create($name, $chatId, $userId)
    {
        $conversation = new Conversation();
        $currDatetime = date('Y-m-d H:i:s');
        $conversation->db->insert($conversation->tableName, [
            'name' => $name,
            'chat_id' => $chatId,
            'user_id' => $userId,
            'state' => '{}',
            'created_at' => $currDatetime,
            'updated_at' => $currDatetime
        ]);

        $id = $conversation->db->insertId();
        return Conversation::callById($id);
    }

    public static function callOrCreate($name, $chatId, $userId)
    {
        try {

            $conversation = Conversation::call($chatId, $userId);
            return $conversation;

        } catch(ConversationNotFoundException $err) {

            $conversation = Conversation::create($name, $chatId, $userId);
            return $conversation;

        }
    }

    private function fillFromDb($row)
    {
        $this->id = (int) $row['id'];
        $this->name = $row['name'];
        $this->chatId = (int) $row['chat_id'];
        $this->userId = (int) $row['user_id'];
        $this->step = (int) $row['step'];
        $this->state = json_decode($row['state'], true);
        $this->status = $row['status'];
    }

    public function isExists()
    {
        return $this->id ? true : false;
    }

    public function nextStep()
    {
        if(!$this->isExists()) {
            throw new ConversationNotFoundException('Conversation not found');
        }
        $this->step++;
    }

    public function commit()
    {
        if(!$this->isExists()) {
            throw new ConversationNotFoundException('Conversation not found');
        }

        $this->db->update($this->tableName, [
            'step' => $this->step,
            'state' => json_encode($this->state),
            'updated_at' => date('Y-m-d H:i:s')
        ], "id=%i", $this->id);
    }

    public function cancel()
    {
        if(!$this->isExists()) {
            throw new ConversationNotFoundException('Conversation not found');
        }

        $this->db->delete($this->tableName, 'id=%i', $this->id);
    }

    public function done()
    {
        if(!$this->isExists()) {
            throw new ConversationNotFoundException('Conversation not found');
        }

        $this->db->update($this->tableName, [
            'step' => $this->step,
            'state' => json_encode($this->state),
            'status' => 'done',
            'updated_at' => date('Y-m-d H:i:s')
        ], "id=%i", $this->id);
    }

    public function __get($key)
    {
        if(array_key_exists($key, $this->state)) {
            return $this->state[$key];
        }
        return null;
    }

    public function __set($key, $value)
    {
        $this->state[$key] = $value;
    }

    public function push($key, $value)
    {
        array_push($this->state[$key], $value);
    }

    public function getStateArray()
    {
        return $this->state;
    }
}