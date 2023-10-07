<?php
error_reporting(E_ALL);

require __DIR__.'/../bootstrap.php';

if(isset($_GET['page'])) {
    $page = $_GET['page'];
    require __DIR__."/Views/$page.php";
    exit();
}

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
    <h2>App</h2>
    <div>
        <table>
            <thead>
                <tr>
                    <th style="width:1px">No.</th>
                    <th>Item</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <a href="<?=publicUrl('/App/?page=test')?>"><?=publicUrl('/App/?page=test')?></a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>
                        <a href="<?=publicUrl('/App/?page=error-log')?>"><?=publicUrl('/App/?page=error-log')?></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>