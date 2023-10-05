<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/..');
try {
    $dotenv->load();
    
    $dotenv->required('APP_MODE');
    
    $dotenv->required('PUBLIC_URL');
    $dotenv->required('DEV_PUBLIC_URL');

    $dotenv->required('BOT_TOKEN');
    $dotenv->required('BOT_USERNAME');
    $dotenv->required('BOT_HOOK_URL');
    $dotenv->required('BOT_COMMANDS_PATH');
    
    $dotenv->required('BOT_USE_MYSQL')->isBoolean();
    $dotenv->required('BOT_MYSQL_PREFIX');
    
    $dotenv->required('MYSQL_HOST');
    $dotenv->ifPresent('MYSQL_PORT')->isInteger();
    $dotenv->required('MYSQL_USERNAME');
    $dotenv->required('MYSQL_PASSWORD');
    $dotenv->required('MYSQL_DATABASE');
} catch(\Exception $err) {
    dd($err->getMessage());
}