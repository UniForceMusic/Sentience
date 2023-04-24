<?php

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
}

$service = new Service();

?>