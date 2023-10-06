<?php
use App\Cores\MySqlErrorLog;

$log = new MySqlErrorLog('server');

try {

    $data = Contact::$data;
    echo $data;

} catch(\Error $err) {

    $log->catch($err);
    $log->message = $err->getMessage();
    $log->record();

}