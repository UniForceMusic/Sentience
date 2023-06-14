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
    public function dbConn(): mysqli
    {
        return mysqli_connect();
    }

    public function appVersion(): string
    {
        return 'appVersion';
    }

    public function appName(): string
    {
        return 'Sentience';
    }

    public function appAuthor(): string
    {
        return 'UniForceMusic';
    }

    public function database(): Database
    {
        $username = 'root';
        $password = '';
        $engine = 'mysql';
        $host = '127.0.0.1';
        $port = '3306';
        $name = 'sentience';

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