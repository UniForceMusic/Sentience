<?php

namespace src\database;

use PDO;
use PDOStatement;
use src\app\Stdio;
use src\database\queries\Query;
use src\database\querybuilders\MySQL;
use src\database\querybuilders\QueryBuilderInterface;
use src\exceptions\DatabaseException;
use src\exceptions\SqlException;

class Database
{
    public const MYSQL = 'mysql';

    protected PDO $pdo;
    protected string $type;
    protected bool $debug;

    public function __construct(string $dsn, string $username, string $password, bool $debug = false)
    {
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);

        $this->pdo = $pdo;
        $this->type = $this->getTypeByDsn($dsn);
        $this->debug = $debug;
    }

    public function exec(string $query, ?array $params = []): PDOStatement
    {
        $statement = $this->pdo->prepare($query);
        if (!$statement->execute($params)) {
            throw new SqlException($statement);
        }

        if ($this->debug) {
            Stdio::errorFLn('Query: %s', $this->formatQueryString($statement, $params));
        }

        return $statement;
    }

    public function beginTransaction(): void
    {
        $this->exec('begin;');
    }

    public function commitTransaction(): void
    {
        $this->exec('commit;');
    }

    public function rollbackTransaction(): void
    {
        $this->exec('rollback;');
    }

    public function query(): Query
    {
        return new Query($this);
    }

    public function getQueryBuilder(): ?QueryBuilderInterface
    {
        if ($this->type == $this::MYSQL) {
            return new MySQL();
        }

        throw new DatabaseException(sprintf('Database engine: "%s" is not a valid engine', $this->type));
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

    protected function formatQueryString(PDOStatement $statement, array $params): string
    {
        $queryString = $statement->queryString;

        $questionMarks = [];
        foreach ($params as $param) {
            $questionMarks[] = '?';
        }

        $params = array_map(
            function ($param) {
                if (is_string($param)) {
                    return sprintf('"%s"', addslashes($param));
                }

                if (is_null($param)) {
                    return 'NULL';
                }

                if (is_bool($param)) {
                    return ($param) ? '1' : '0';
                }

                return (string) $param;
            },
            $params
        );

        foreach ($questionMarks as $index => $questionMark) {
            $queryString = preg_replace('/\?/', $params[$index], $queryString, 1);
        }

        return $queryString;
    }
}
