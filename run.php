<?php
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

file_put_contents("run.lock", strtotime('+5 minutes'));

if (file_exists(__DIR__ . "/logs/sync.log")) {
    $config['date_from'] = file_get_contents(__DIR__ . "/logs/sync.log");
}

$apiHelper = new ApiHelper($config);

if ($apiHelper->processXMLOrders()) {
    unlink($lockFile);
    file_put_contents(__DIR__ . "/logs/sync.log", date('Y-m-d H:i:s'));
}