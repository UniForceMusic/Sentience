<?php

require_once 'requires.php';

use src\app\CliApp;
use src\app\HttpApp;

if (!$_ENV['SYNTAX_REPORTING'] && $_ENV['ERROR_REPORTING']) {
    error_reporting(E_ERROR | E_WARNING);
}

if (!$_ENV['SYNTAX_REPORTING'] && !$_ENV['ERROR_REPORTING']) {
    error_reporting(0);
}

$app = isCli()
    ? new CliApp($commands, $service, $argv)
    : new HttpApp($routes, $service);

$app->execute();
