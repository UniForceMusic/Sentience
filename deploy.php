<?php

exec('composer install');
exec('php index.php database/init');
exec('php index.php database/migrate');
