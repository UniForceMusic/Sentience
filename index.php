<?php

require_once 'settings.php';
require_once 'vendor/autoload.php';
require_once 'routes.php';
require_once 'service.php';

use src\app\App;

$app = new App($routes, $service);
$app->execute();

?>