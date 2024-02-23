<?php

use src\database\Database;

/**
 * A class to define your global variables
 * These variables will be injected as arguments in the method or function
 * 
 * When a method or function calls for $appName, the public function appName()
 * Will be executed
 * 
 * Only the variables that are listed as arguments will be injected
 * A good use case would be putting the db connection variable here
 */

class Service
{
    public function service(): static
    {
        return $this;
    }

    public function appName(): string
    {
        return env('APP_NAME', 'App name not set');
    }

    public function appVersion(): string
    {
        return env('APP_VERSION', 'App version not set');
    }

    public function database(): Database
    {
        $username = env('DB_USERNAME', '');
        $password = env('DB_PASSWORD', '');
        $engine = env('DB_ENGINE', '');
        $host = env('DB_HOST', '');
        $port = env('DB_PORT', '');
        $name = env('DB_NAME', '');
        $debug = env('DB_DEBUG', '');

        return new Database(
            $engine,
            $host,
            $port,
            $username,
            $password,
            $name,
            $debug,
        );
    }
}

$service = new Service();
