<?php
namespace App\Cores;

use MeekroDB;

class DB extends MeekroDB
{
    public function __construct()
    {
        $host = env('MYSQL_HOST', 'localhost');
        $user = env('MYSQL_USERNAME', 'root');
        $password = env('MYSQL_PASSWORD', '');
        $dbName = env('MYSQL_DATABASE', '');
        parent::__construct($host, $user, $password, $dbName);

        $this->connect_options = [
            MYSQLI_OPT_CONNECT_TIMEOUT => 10
        ];
    }
}