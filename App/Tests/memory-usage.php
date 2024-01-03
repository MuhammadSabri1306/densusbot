<?php

if(!isset($_GET['key']) || $_GET['key'] != 'pamadminmonitor123') {
    http_response_code(404);
    exit();
}

function _getServerLoadLinuxData() {
    if(!is_readable("/proc/stat")) return null;
    
    $stats = @file_get_contents("/proc/stat");
    if($stats === false) return null;

    $stats = preg_replace("/[[:blank:]]+/", " ", $stats);

    $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
    $stats = explode("\n", $stats);

    foreach($stats as $statLine) {
        $statLineData = explode(" ", trim($statLine));

        if( (count($statLineData) >= 5) && ($statLineData[0] == "cpu") ) {
            return [
                $statLineData[1],
                $statLineData[2],
                $statLineData[3],
                $statLineData[4],
            ];
        }
    }
    return null;
}

// Returns server load in percent (just number, without percent sign)
function getServerLoad()
{
    $load = null;

    if(stristr(PHP_OS, "win")) {

        $cmd = "wmic cpu get loadpercentage /all";
        @exec($cmd, $output);

        if($output) {
            foreach ($output as $line) {
                if($line && preg_match("/^[0-9]+\$/", $line)) {
                    $load = $line;
                    break;
                }
            }
        }

    } else {
        if (is_readable("/proc/stat")) {

            // Collect 2 samples - each with 1 second period
            // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
            $statData1 = _getServerLoadLinuxData();
            sleep(1);
            $statData2 = _getServerLoadLinuxData();

            if( (!is_null($statData1)) && (!is_null($statData2)) ) {
                // Get difference
                $statData2[0] -= $statData1[0];
                $statData2[1] -= $statData1[1];
                $statData2[2] -= $statData1[2];
                $statData2[3] -= $statData1[3];

                // Sum up the 4 values for User, Nice, System and Idle and calculate
                // the percentage of idle time (which is part of the 4 values!)
                $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

                // Invert percentage to get CPU time, not idle time
                $load = 100 - ($statData2[3] * 100 / $cpuTime);
            }
        }
    }

    return $load;
}

$data = [
    'success' => true,
    'message' => 'Success to get data.',
    'performance' => null
];

try {
    $data['performance'] = [
        'memory_usage' => [
            'value' => memory_get_usage(),
            'unit' => 'bytes'
        ],
        'cpu_usage_percent' => [
            'value' => getServerLoad(),
            'unit' => '%'
        ]
    ];

    $jsonData = json_encode($data);

} catch (\Exception $err) {
    $data['success'] = false;
    $data['message'] = strval($err);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);