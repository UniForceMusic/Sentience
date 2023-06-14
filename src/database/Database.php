<?php

namespace src\database;

use PDO;
use PDOStatement;
use src\database\exceptions\SQLException;
use src\database\queries\Query;

class Database
{
    public const MYSQL = 'mysql';
    public const POSTGRES = 'postgres';

    protected PDO $pdo;
    protected string $type;

    public function __construct(string $dsn, string $username, string $password)
    {
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);

        $this->pdo = $pdo;
        $this->type = $this->getTypeByDsn($dsn);
    }

    public function exec(string $query, ?array $params = []): PDOStatement
    {
        $statement = $this->pdo->prepare($query);
        if (!$statement->execute($params)) {
            throw new SQLException($statement);
        }

        return $statement;
    }

    public function query(): Query
    {
        return new Query($this);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLastInsertedId(string $pk = null): int
    {
        return $this->pdo->lastInsertId($pk);
    }

    protected function getTypeByDsn(string $dsn)
    {
        return trim(
            strtolower(
                explode(':', $dsn)[0]
            )
        );
    }
}

?>