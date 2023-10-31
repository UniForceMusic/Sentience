<?php

namespace src\app;

use Throwable;
use Service;
use src\router\Command;
use src\router\Route;

interface AppInterface
{
    public function execute(): void;

    public function handleException(Throwable $error): void;
}
