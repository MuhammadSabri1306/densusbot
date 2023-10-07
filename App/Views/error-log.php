<?php
use App\Cores\MySqlErrorLog;

try {

    $logs = MySqlErrorLog::getAllLogs(function($db, $tableName) {
        if(isset($_GET['id'])) {
            return $db->query("SELECT * FROM $tableName WHERE id=%i", $_GET['id']);
        }
        return $db->query("SELECT * FROM $tableName ORDER BY id DESC LIMIT 10");
    });
    
    ?><head>
        <style>
            body.error-log {
                padding: 10rem;
            }
            body.error-log h2 {
                text-align: center;
            }
            body.error-log .text-alert {
                background-color: #f6f8fa;
                padding: 20px 10px 0 10px;
                margin: 30px 0 0 0;
            }
            body.error-log pre {
                background-color: #f6f8fa;
                padding: 10px;
                border-bottom: 1px solid #ddd;
                margin: 0;
            }
            body.error-log strong {
                color: #e91e63;
            }
        </style>
    </head>
    <body class="error-log">
        <h2>App</h2><?php
    
    foreach($logs as $log):
        if(isset($log['traced_data']['trace_list'])):
    
            ?><p class="text-alert">
            <span><?=$log['created_at']?></span>
            <strong>Error <?=$log['type']?>:</strong>
            <span><?=$log['message']?></span>
        </p>
        <div><?php
    
            foreach($log['traced_data']['trace_list'] as $item):
    
                ?><pre><?=$item['file']?> at <strong>line <?=$item['line']?></strong></pre><?php
    
            endforeach;
    
        ?></div><?php
    
        else:
        
        ?><p class="text-alert">
            <span><?=$log['created_at']?></span>
            <strong>Error <?=$log['type']?>:</strong>
            <span><?=$log['message']?></span>
        </p>
        <div>
            <pre><?php
    
        var_dump($log['traced_data']);
    
            ?></pre>
        </div><?php
    
        endif;
    
    endforeach;
    
    ?></body><?php

} catch(\Error $err) {
    dd($err);
}
