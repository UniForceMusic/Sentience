<?php

require_once 'requires.php';

use src\app\CliApp;
use src\app\HttpApp;

if (isCli()) {
    $app = new CliApp($commands, $service, $argv);
} else {
    $app = new HttpApp($routes, $service);
}

$app->execute();
