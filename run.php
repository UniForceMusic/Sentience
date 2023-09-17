<?php

if (!file_exists('vendor/autoload.php')) {
    exec('composer install');
}

exec('php -S localhost:8000 index.php');
