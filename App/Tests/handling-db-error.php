<?php

use App\Cores\Model;
use App\Cores\MySqlErrorLog;
use App\Models\Regional;

// Regional::getCodeOrdered();

try {
    
    $regional = Regional::getAll();

// } catch(\Exception $err) {
//     $log = new MySqlErrorLog('server');
//     $log->catchBasicError($err);
//     dd($log->get());
} catch(\MeekroDBException $err) {
    $log = new MySqlErrorLog('server');
    $log->catchBasicError($err);
    dd($log->get());
}