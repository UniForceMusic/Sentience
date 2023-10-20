<?php

exec('composer install --no-dev');
exec('php index.php database/init');
exec('php index.php database/migrate');
