<?php
$startTime = date('Y-m-d H:i:s');

require(__DIR__ . "/classes/ApiHelper.php");
require(__DIR__ . "/config/config.php");

if (! file_exists(__DIR__ . "/logs")) {
    mkdir(__DIR__ . "/logs");
}

if (file_exists(__DIR__ . "/logs/cookie.txt")) {
    unlink(__DIR__ . "/logs/cookie.txt");
}

$lockFile = __DIR__ . "/run.lock";

if (file_exists($lockFile)) {
    if ((int)file_get_contents($lockFile) > time()) {
        echo "script is busy";
        exit();
    }
}

file_put_contents($lockFile, strtotime('+5 minutes'));

if (file_exists(__DIR__ . "/logs/sync.log")) {
    $lastSyncTime = file_get_contents(__DIR__ . "/logs/sync.log");
    $lastSyncTime = new DateTime($lastSyncTime);
    $lastSyncTime->sub(new DateInterval('PT1M'));
    $config['date_from'] = $lastSyncTime->format('Y-m-d H:i:s');
}

$apiHelper = new ApiHelper($config);

if ($apiHelper->processXMLOrders()) {
    unlink($lockFile);
    file_put_contents(__DIR__ . "/logs/sync.log", $startTime);
}
