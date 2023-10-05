<?php

$useMysql = env('BOT_USE_MYSQL', false);
$mysqlPrefix = env('BOT_MYSQL_PREFIX', null);
$mysqlConfig = [
   'host'     => env('MYSQL_HOST', 'localhost'),
   'port'     => env('MYSQL_PORT', 3306),
   'user'     => env('MYSQL_USERNAME', 'root'),
   'password' => env('MYSQL_PASSWORD', ''),
   'database' => env('MYSQL_DATABASE', '')
];

dd($useMysql, $mysqlPrefix, $mysqlConfig);