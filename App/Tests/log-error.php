<?php
use App\Cores\MySqlErrorLog;

$log = new MySqlErrorLog('server');

try {

    $data = Contact::$data;
    echo $data;

} catch(\Error $err) {

    $log->catchBasicError($err);
    $log->record();
    dd($log);
    // dd($err->getTrace(), $err);

}