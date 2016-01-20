<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

set_time_limit(0);

$paths = array(
    'short' => __DIR__.'/../app/',
    'long' => __DIR__.'/../../../../../app/',
    'vendor' => __DIR__.'/../../../../../../../app/',
);

$app_path = null;

$files = ['autoload.php', 'bootstrap.php.cache', 'AppKernel.php'];

foreach ($paths as $path) {
    foreach ($files as $file) {
        $app_path = (@include_once $path.$file);
    }
    if ($app_path !== false) {
        break;
    }
}

/*
 * Look both in $_SERVER, $_POST and $argv after some data.
 */
$data = null;
if (isset($_SERVER['DEFERRED_DATA'])) {
    $data = $_SERVER['DEFERRED_DATA'];
} elseif (isset($_POST['DEFERRED_DATA'])) {
    $data = $_POST['DEFERRED_DATA'];
} elseif (isset($argv)) {
    if (isset($argv[1])) {
        $data = $argv[1];
    }
}

if ($data === null) {
    trigger_error('No message data found', E_USER_WARNING);
    exit(1);
}

$message = base64_decode($data);
$headers = array();
$body = null;
$lines = explode("\n", $message);
foreach ($lines as $i => $line) {
    if ($line == '') {
        $body = $lines[$i + 1];
        break;
    }
    list($name, $value) = explode(':', $line, 2);
    $headers[$name] = trim($value);
}

$input = new ArgvInput([$app_path.'console', 'happyr:deferred-message:dispatch', $headers['queue_name'], $body]);

$kernel = new AppKernel('prod', false);
$application = new Application($kernel);
$application->setAutoExit(true);
$application->run($input);
