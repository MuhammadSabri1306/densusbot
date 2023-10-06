<?php
namespace App\Models;

use App\Cores\Model;

class Regional extends Model
{
    public static $table = 'regional';
    
    public static function getSnameOrdered()
    {
        $regionals = static::query(function ($db, $table) {
            return $db->query("SELECT * FROM $table ORDER BY sname");
        });

        return $regionals;
    }
}