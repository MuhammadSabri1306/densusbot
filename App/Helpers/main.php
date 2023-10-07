<?php

function useHelper($helperName) {
    require_once __DIR__."/$helperName.php";
}

function env($key, $default = null) {
    if(isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    return $default;
}

function publicUrl($path) {
    $appMode = env('APP_MODE');
    $baseProdUrl = env('PUBLIC_URL', 'http://localhost/densus_bot');
    $baseDevUrl = env('DEV_PUBLIC_URL', 'http://localhost/densus_bot');
    $baseUrl = $appMode == 'production' ? $baseProdUrl : $baseDevUrl;

    if($path[0] == '/') $path = substr($path, 1);
    return "$baseUrl/$path";
}

function dd(...$vars) {
    echo '<style>';
    echo 'pre { background-color: #f6f8fa; padding: 10px; }';
    echo 'strong { color: #e91e63; }';
    echo '</style>';

    foreach ($vars as $var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
    die();
}

function dd_json($vars) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($vars, JSON_INVALID_UTF8_IGNORE);
}