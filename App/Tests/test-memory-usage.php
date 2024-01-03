<?php

$response = file_get_contents('http://10.60.164.18/api_monitoring/host-performance.php?key=pamadminmonitor123');
dd_json($response);