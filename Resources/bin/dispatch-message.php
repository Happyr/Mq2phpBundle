<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

set_time_limit(0);

// Possible paths for the app folder
$paths = array(
    'short' => __DIR__.'/../app/',
    'long' => __DIR__.'/../../../../../app/',
    'vendor' => __DIR__.'/../../../../../../../app/',
);

$appPath = null;
$files = ['autoload.php', 'bootstrap.php.cache', 'AppKernel.php'];

// Find the correct path
foreach ($paths as $path) {
    foreach ($files as $file) {
        $appPath = (@include_once $path.$file);
    }
    if ($appPath !== false) {
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
    // if shell
    if (isset($argv[1])) {
        $data = urldecode($argv[1]);
    }
}

if ($data === null) {
    trigger_error('No message data found', E_USER_WARNING);
    http_response_code(400);
    exit(1);
}

// Decode the message and get the data
$message = json_decode(base64_decode($data), true);
$headers = $message['headers'];
$body = $message['body'];

$queueName = null;
foreach ($headers as $header){
    if ($header['key'] === 'queue_name') {
        $queueName = $header['value'];
        break;
    }
}

// Prepare to call a Symfony command
$input = new ArgvInput([$appPath.'console', 'happyr:mq2php:dispatch', $queueName, $body]);

$kernel = new AppKernel('prod', false);
$application = new Application($kernel);
$application->setAutoExit(false);
$exitCode = $application->run($input);

if ($exitCode != 0) {
    trigger_error('Exception was thrown when executing the command', E_USER_WARNING);
    http_response_code(500);

    exit($exitCode);
}
