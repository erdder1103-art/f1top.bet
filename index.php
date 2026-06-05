<?php
// A/B site switch front controller
// mode A -> /site-a/
// mode B -> /site-b/

$configFile = __DIR__ . '/config.json';
$mode = 'A';

if (is_file($configFile)) {
    $json = file_get_contents($configFile);
    $config = json_decode($json, true);
    if (is_array($config) && isset($config['mode']) && strtoupper($config['mode']) === 'B') {
        $mode = 'B';
    }
}

$target = ($mode === 'B') ? '/site-b/' : '/site-a/';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Location: ' . $target, true, 302);
exit;
