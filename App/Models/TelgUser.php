<?php
namespace App\Models;

use App\Cores\Model;

class TelgUser extends Model
{
    public static $table = 'telg_user';

    public static function find($id)
    {
        $data = static::query(function ($db, $table) use ($id) {
            return $db->queryFirstRow("SELECT * FROM $table WHERE id=%i", $id);
        });

        if($data && isset($data['telegram_data'])) {
            $data['telegram_data'] = json_decode($data['telegram_data'], true);
        }
        return $data;
    }

    public static function findByChatId($chatId)
    {
        $data = static::query(function ($db, $table) use ($chatId) {
            return $db->queryFirstRow("SELECT * FROM $table WHERE chat_id=%s", $chatId);
        });

        if($data && isset($data['telegram_data'])) {
            $data['telegram_data'] = json_decode($data['telegram_data'], true);
        }
        return $data;
    }

    public static function getAll()
    {
        $rows = static::query(function ($db, $table) {
            return $db->query("SELECT * FROM $table");
        });
        
        return array_map(function($item) {
            if($item && isset($item['telegram_data'])) {
                $item['telegram_data'] = json_decode($item['telegram_data'], true);
            }
            return $item;
        }, $rows);
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
            return $id ? TelgUser::find($id) : null;
        });
    }
}