<?php
namespace App\Cores;

use App\Cores\MySqlErrorLog;

class Controller
{
    public static function catchBasicError($err)
    {
        $log = new MySqlErrorLog('server');
        $log->catchBasicError($err);
        $log->record();
        return $log;
    }
}