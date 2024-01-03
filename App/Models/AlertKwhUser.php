<?php
namespace App\Models;

use App\Cores\Model;
use App\Models\TelgUser;

class AlertKwhUser extends Model
{
    public static $table = 'telg_user_alert_kwh';

    public static function getTelgUserSelect(): string
    {
        $table = AlertKwhUser::$table;
        $tableTelgUser = TelgUser::$table;
        $fieldTelgUser = ['chat_id', 'type', 'telegram_data', 'regist_id', 'level', 'regional_id', 'witel_id'];

        $selectedFieldsArr = ["$table.*"];
        foreach($fieldTelgUser as $field) {
            array_push($selectedFieldsArr, "$tableTelgUser.$field");
        }
        $selectedFields = implode(', ', $selectedFieldsArr);

        return "SELECT $selectedFields FROM $table JOIN $tableTelgUser ON $tableTelgUser.id=$table.telg_user_id";
    }

    public static function find($id)
    {
        $data = static::query(function ($db, $table) use ($id) {
            $joinQuery = AlertKwhUser::getTelgUserSelect();
            return $db->queryFirstRow("$joinQuery WHERE $table.id=%i", $id);
        });

        if($data && isset($data['telegram_data'])) {
            $data['telegram_data'] = json_decode($data['telegram_data'], true);
        }
        return $data;
    }

    public static function getAll()
    {
        $rows = static::query(function ($db, $table) {
            $joinQuery = AlertKwhUser::getTelgUserSelect();
            return $db->query($joinQuery);
        });
        
        return array_map(function($item) {
            if($item && isset($item['telegram_data'])) {
                $item['telegram_data'] = json_decode($item['telegram_data'], true);
            }
            return $item;
        }, $rows);
    }

    public static function getIsAlertOn()
    {
        $rows = static::query(function ($db, $table) {
            $joinQuery = AlertKwhUser::getTelgUserSelect();
            return $db->query("$query WHERE send_alert=1");
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
        return static::query(function ($db, $table) use ($data) {
            $db->insert($table, $data);
            $id = $db->insertId();
            return $id ? AlertKwhUser::find($id) : null;
        });
    }
}