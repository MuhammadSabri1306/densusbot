<?php
namespace App\Models;

use App\Cores\Model;
use App\Models\Regional;

class Witel extends Model
{
    public static $table = 'witel';
    
    public static function getCodeOrdered($regionalId)
    {
        return Witel::query(function ($db, $table) use ($regionalId) {
            return $db->query("SELECT * FROM $table WHERE regional_id=%i ORDER BY code", $regionalId);
        });
    }
}