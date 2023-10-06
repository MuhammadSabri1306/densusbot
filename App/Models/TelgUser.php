<?php
namespace App\Models;

use App\Cores\Model;

class TelgUser extends Model
{
    public static $table = 'telg_user';

    public static function find($id)
    {
        $row = static::query(function ($db, $table) use ($id) {
            return $db->queryFirstRow("SELECT * FROM $table WHERE id=%i", $id);
            if(!$data) return null;
    
            $data['data'] = json_decode($data['data'], true);
            return $data;
        });
    }

    public static function findByChatId($chatId)
    {
        $row = static::query(function ($db, $table) use ($chatId) {
            return $db->queryFirstRow("SELECT * FROM $table WHERE chat_id=%s", $chatId);
            if(!$data) return null;
    
            $data['data'] = json_decode($data['data'], true);
            return $data;
        });
    }

    public static function create(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        if(isset($data['telegram_data'])) {
            $data['telegram_data'] = json_encode($data['telegram_data']);
        }
        return static::query(function ($db, $table) use ($data) {
            $db->insert($table, $data);
            $id = $db->insertId();
            return $id ? static::find($id) : null;
        });
    }
}