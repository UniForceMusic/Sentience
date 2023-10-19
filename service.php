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
    public function appName(): string
    {
        return $_ENV['APP_NAME'] ?? 'App name not set';
    }

    public function appVersion(): string
    {
        return $_ENV['APP_VERSION'] ?? 'App version not set';
    }

    public function database(): Database
    {
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $engine = $_ENV['DB_ENGINE'];
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $name = $_ENV['DB_NAME'];
        $debug = $_ENV['DB_DEBUG'];

        return new Database(
            $username,
            $password,
            $engine,
            $host,
            $port,
            $name,
            $debug,
        );
    }
}

$service = new Service();
