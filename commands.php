<?php

use src\controllers\ExampleController;
use src\controllers\ManagementController;
use src\router\Command;

$commands = [
    Command::create()
        ->setArgument('server/start')
        ->setCallable([ManagementController::class, 'startServer']),

    Command::create()
        ->setArgument('example')
        ->setCallable([ExampleController::class, 'exampleCli']),

    Command::create()
        ->setArgument('database/create')
        ->setCallable([ManagementController::class, 'createDatabase']),

    Command::create()
        ->setArgument('database/init')
        ->setCallable([ManagementController::class, 'initDatabase']),

    Command::create()
        ->setArgument('database/migrate')
        ->setCallable([ManagementController::class, 'runMigrations']),

    Command::create()
        ->setArgument('models/init')
        ->setCallable([ManagementController::class, 'initModel']),

    Command::create()
        ->setArgument('tests')
        ->setCallable([ManagementController::class, 'runTests']),

    Command::create()
        ->setArgument('docker/init')
        ->setCallable([ManagementController::class, 'dockerInit']),

    Command::create()
        ->setArgument('docker/up')
        ->setCallable([ManagementController::class, 'dockerUp']),

    Command::create()
        ->setArgument('docker/down')
        ->setCallable([ManagementController::class, 'dockerDown']),

    Command::create()
        ->setArgument('docker/rebuild')
        ->setCallable([ManagementController::class, 'dockerRebuild']),

    Command::create()
        ->setArgument('dotenv/fix')
        ->setCallable([ManagementController::class, 'fixDotEnv']),
];
