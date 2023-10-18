<?php

namespace src\database;

use Throwable;
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
    protected string $initDsn;
    protected string $initUsername;
    protected string $initPassword;
    protected bool $initDebug;

    public function __construct(string $dsn, string $username, string $password, bool $debug = false)
    {
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);

        $this->pdo = $pdo;
        $this->type = $this->getTypeByDsn($dsn);
        $this->debug = $debug;

        $this->initDsn = $dsn;
        $this->initUsername = $username;
        $this->initPassword = $password;
        $this->initDebug = $debug;
    }

    public function createInstance(): static
    {
        return new static(
            $this->initDsn,
            $this->initUsername,
            $this->initPassword,
            $this->initDebug
        );
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

    public function transactionAsCallback(callable $callable): void
    {
        $connection = $this->createInstance();
        $connection->beginTransaction();

        try {
            $callable($connection);
            $connection->commitTransaction();
        } catch (Throwable $err) {
            $connection->rollbackTransaction();
            throw $err;
        }
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
                    return ($param) ? 'true' : 'false';
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
