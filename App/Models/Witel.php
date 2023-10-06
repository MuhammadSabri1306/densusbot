<?php
namespace App\Models;

use App\Cores\Model;
use App\Models\Regional;

class Witel extends Model
{
    public static $table = 'witel';
    public static $usedRelations = [];

    public static function getRelations()
    {
        return [
            'regional' => Model::createRelation(Regional::class, 'regional_id')
        ];
    }
    
    public static function getSnameOrdered()
    {
        $regionals = Witel::query(function ($db, $table) {
            return $db->query("SELECT * FROM $table ORDER BY sname");
        });

        return $regionals;
    }
}