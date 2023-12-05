<?php

namespace src\exceptions;

use Exception;
use PDOStatement;

class SqlException extends Exception
{
    public function __construct(PDOStatement $statement)
    {
        $error = $statement->errorInfo();

        parent::__construct(
            sprintf(
                'error: (%s) query: (%s)',
                ($error[2] ?? $error[0]),
                $statement->queryString
            )
        );
    }
}
