<?php

namespace src\exceptions;

use Exception;
use PDOStatement;

class SqlException extends Exception
{
    public function __construct(PDOStatement $statement)
    {
        $error = $statement->errorInfo();

        parent::__construct($error[2] ?? $error[0]);
    }
}
