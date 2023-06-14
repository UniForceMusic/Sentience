<?php

namespace src\database\exceptions;

use Exception;
use PDOStatement;

class SQLException extends Exception
{
    public function __construct(PDOStatement $statement)
    {
        $error = $statement->errorInfo();

        if (count($error) < 3) {
            parent::__construct(sprintf('Error trying to execute query: "%s"', $statement->queryString));
        }

        parent::__construct(strval($error[2]));
    }
}