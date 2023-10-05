<?php
require __DIR__.'/../bootstrap.php';

if(isset($_GET['test'])) {
    $testFilePath = __DIR__.'/Tests/'.$_GET['test'].'.php';
    if(file_exists($testFilePath)) {
        require $testFilePath;
    } else {
        dd($testFilePath);
    }
    exit();
}

function listFolderFiles($dir){
    $ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    if(count($ffs) < 1)
        return;

    $trees = [];
    foreach($ffs as $ff){
        array_push($trees, $ff);
        if(is_dir($dir.'/'.$ff)) $trees = [...$trees, listFolderFiles($dir.'/'.$ff)];
    }
    return $trees;
}

function getTestUrl($filepath) {
    $appMode = env('APP_MODE');
    $baseUrl = env('PUBLIC_URL', 'http://localhost/densus_bot');
    $baseDevUrl = env('DEV_PUBLIC_URL', 'http://localhost/densus_bot');
    $fileName = str_replace('.php', '', $filepath);
    
    return $appMode == 'production' ? "$baseUrl/App/?test=$fileName" : "$baseDevUrl/App/?test=$fileName";
}

$pathTree = listFolderFiles(__DIR__.'/Tests');

?><head>
    <style>
        body.path-tree {
            padding: 10rem;
        }
        body.path-tree h2 {
            text-align: center;
        }
        body.path-tree table {
            border-collapse: collapse;
            width: 100%;
        }
        body.path-tree td,
        body.path-tree th {
            border: 1px solid #ddd;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body class="path-tree">
    <h2>Tests List</h2>
    <div>
        <table>
            <thead>
                <tr>
                    <th style="width:1px">No.</th>
                    <th>Test Item</th>
                </tr>
            </thead>
            <tbody><?php

foreach($pathTree as $index => $path):

                ?><tr>
                    <td><?=($index + 1)?></td>
                    <td>
                        <a href="<?=getTestUrl($path)?>"><?=$path?></a>
                    </td>
                </tr><?php

endforeach;

            ?></tbody>
        </table>
    </div>
</body>