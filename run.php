<?php

include_once 'requires.php';

exec(
    sprintf(
        'php -S %s:%s %s/index.php',
        $_ENV['HOST'],
        $_ENV['PORT'],
        BASEDIR
    )
);
