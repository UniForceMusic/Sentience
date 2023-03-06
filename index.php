<?php

require_once 'settings.php';
require_once BASEDIR . "vendor/autoload.php";

use src\util\Url;

echo Url::getRequestUri();

?>