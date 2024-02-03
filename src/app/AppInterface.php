<?php

namespace src\app;

use Throwable;

interface AppInterface
{
    public function execute(): void;

    public function handleException(Throwable $error): void;
}
