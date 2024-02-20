<?php

require_once 'requires.php';

use src\app\CliApp;
use src\app\HttpApp;

$app = isCli()
    ? new CliApp($commands, $service, $argv)
    : new HttpApp($routes, $service);

$app->execute();
