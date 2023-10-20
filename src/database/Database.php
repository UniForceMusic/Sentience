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
    protected string $engine;
    protected string $host;
    protected int $port;
    protected string $username;
    protected string $password;
    protected string $name;
    protected bool $debug;

    public function __construct(
        string $engine,
        string $host,
        int $port,
        string $username,
        string $password,
        string $name,
        bool $debug = false
    ) {
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            $engine,
            $host,
            $port,
            $name,
        );

        $this->pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);

        $this->engine = $engine;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->debug = $debug;
    }

    public function createInstance(): static
    {
        return new static(
            $this->engine,
            $this->host,
            $this->port,
            $this->username,
            $this->password,
            $this->name,
            $this->debug,
        );
    }

    public function exec(string $query, ?array $params = []): PDOStatement
    {
        $statement = $this->pdo->prepare($query);
        if (!$statement->execute($params)) {
            throw new SqlException($statement);
        }

        if ($this->debug) {
            Stdio::errorFLn('Query: %s', $this->printDebug($statement, $params));
        }

        return $statement;
    }

    public function beginTransaction(): static
    {
        $connection = $this->createInstance();
        $connection->exec('begin;');

        return $connection;
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
        $connection = $this->beginTransaction();

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
        if ($this->engine == $this::MYSQL) {
            return new MySQL();
        }

        throw new DatabaseException(sprintf('Database engine: "%s" is not a valid engine', $this->engine));
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function getLastInsertedId(string $pk = null): int
    {
        return $this->pdo->lastInsertId($pk);
    }

    protected function printDebug(PDOStatement $statement, array $params): string
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryString = $statement->queryString;

        $index = 0;
        return preg_replace_callback(
            '/\?/',
            function ($matches) use ($params, $queryBuilder, &$index) {
                $param = $params[$index];
                $index++;

                if (is_string($param)) {
                    return sprintf('"%s"', addslashes($param));
                }

                return $queryBuilder->castToDatabase($param);
            },
            $queryString
        );
    }
}
