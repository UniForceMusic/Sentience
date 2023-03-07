<?php

require_once 'settings.php';
require_once 'vendor/autoload.php';
require_once 'routes.php';

use src\util\Url;
use src\app\App;

$app = new App($routes);
$app->execute();

?>