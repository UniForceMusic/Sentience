<?php

error_reporting(
    [
        'error' => E_ALL | E_WARNING | E_NOTICE,
        'warning' => E_WARNING | E_NOTICE,
        'notice' => E_NOTICE,
        'none' => 0
    ]
    [env('APP_LOG_LEVEL', 'error')]
);
