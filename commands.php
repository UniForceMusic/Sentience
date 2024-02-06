<?php

use src\controllers\ExampleController;
use src\controllers\ManagementController;
use src\router\Command;

$commands = [
    Command::create()
        ->setCommand('server/start')
        ->setCallable([ManagementController::class, 'startServer']),

    Command::create()
        ->setCommand('example')
        ->setCallable([ExampleController::class, 'exampleCli']),

    Command::create()
        ->setCommand('database/create')
        ->setCallable([ManagementController::class, 'createDatabase']),

    Command::create()
        ->setCommand('database/init')
        ->setCallable([ManagementController::class, 'initDatabase']),

    Command::create()
        ->setCommand('database/migrate')
        ->setCallable([ManagementController::class, 'runMigrations']),

    Command::create()
        ->setCommand('models/init')
        ->setCallable([ManagementController::class, 'initModel']),

    Command::create()
        ->setCommand('tests')
        ->setCallable([ManagementController::class, 'runTests']),

    Command::create()
        ->setCommand('docker/init')
        ->setCallable([ManagementController::class, 'dockerInit']),

    Command::create()
        ->setCommand('docker/up')
        ->setCallable([ManagementController::class, 'dockerUp']),

    Command::create()
        ->setCommand('docker/down')
        ->setCallable([ManagementController::class, 'dockerDown']),

    Command::create()
        ->setCommand('docker/rebuild')
        ->setCallable([ManagementController::class, 'dockerRebuild']),

    Command::create()
        ->setCommand('dotenv/fix')
        ->setCallable([ManagementController::class, 'fixDotEnv']),
];
