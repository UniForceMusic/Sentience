<?php

if (!file_exists('vendor/autoload.php')) {
    exec('composer install');
}
require_once 'vendor/autoload.php';
require_once 'settings.php';
require_once 'commands.php';
require_once 'routes.php';
require_once 'dotenv.php';
require_once 'service.php';
require_once 'src/util/Functions.php';

use src\app\CliApp;
use src\app\HttpApp;

if (isCli()) {
    $app = new CliApp($commands, $service, $argv);
} else {
    $app = new HttpApp($routes, $service);
}

$app->execute();

?>