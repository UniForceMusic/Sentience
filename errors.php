<?php

if (env('APP_LOG_LEVEL') == 'error') {
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
}

if (env('APP_LOG_LEVEL') == 'warning') {
    error_reporting(E_WARNING | E_NOTICE);
}

if (env('APP_LOG_LEVEL') == 'notice') {
    error_reporting(E_NOTICE);
}

if (env('APP_LOG_LEVEL') == 'none') {
    error_reporting(0);
}

