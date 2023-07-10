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
    public function appVersion(): string
    {
        return $_ENV['APP_VERSION'] ?? 'App version not set';
    }

    public function appName(): string
    {
        return $_ENV['APP_NAME'] ?? 'App name not set';
    }

    public function database(): Database
    {
        $username = 'root';
        $password = '';
        $engine = 'mysql';
        $host = '127.0.0.1';
        $port = '3306';
        $name = 'sentience-dev';

        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s",
            $engine,
            $host,
            $port,
            $name
        );

        return new Database(
            $dsn,
            $username,
            $password
        );
    }
}

$service = new Service();

?>