<?php

use src\controllers\ExampleController;
use src\controllers\ManagementController;
use src\router\Command;

$commands = [
    Command::create()
        ->setCommand('example')
        ->setCallable([ExampleController::class, 'exampleCli']),

    Command::create()
        ->setCommand('database/init')
        ->setCallable([ManagementController::class, 'initDatabase']),

    Command::create()
        ->setCommand('database/migrate')
        ->setCallable([ManagementController::class, 'runMigrations']),

    Command::create()
        ->setCommand('models/init')
        ->setCallable([ManagementController::class, 'initModel']),
];

?>