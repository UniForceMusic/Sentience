<?php

namespace src\database\queries;

use Exception;
use PDO;
use PDOStatement;
use src\database\Database;
use src\database\queries\Table as TableTrait;
use src\database\queries\Columns as ColumnsTrait;
use src\database\queries\Values as ValuesTrait;
use src\database\queries\Where as WhereTrait;
use src\database\queries\Limit as LimitTrait;
use src\database\querybuilders\MySQL as MySQLQueryBuilder;


class Query
{
    use TableTrait;
    use ColumnsTrait;
    use ValuesTrait;
    use WhereTrait;
    use LimitTrait;

    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function select(): PDOStatement
    {
        $queryBuilder = $this->getQueryBuilder();
        [$query, $params] = $queryBuilder->select(
            $this->table,
            $this->columns,
            $this->whereConditions,
            $this->whereValues,
            $this->limit
        );

        return $this->database->exec($query, $params);
    }

    public function selectAssoc(): ?array
    {
        $statement = $this->select();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAssocSingle(): ?array
    {
        $statement = $this->select();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function selectAndCount(): int
    {
        $statement = $this->select();

        return $statement->rowCount();
    }

    public function exists(): bool
    {
        $count = $this->selectAndCount();

        return ($count > 0);
    }

    public function insert(): PDOStatement
    {
        $queryBuilder = $this->getQueryBuilder();
        [$query, $params] = $queryBuilder->insert(
            $this->table,
            $this->values
        );

        return $this->database->exec($query, $params);
    }

    public function insertWithLastId(string $primaryKey = null): int
    {
        $statement = $this->insert();

        return $this->database->getLastInsertedId($primaryKey);
    }

    public function update(): PDOStatement
    {
        $queryBuilder = $this->getQueryBuilder();
        [$query, $params] = $queryBuilder->update(
            $this->table,
            $this->values,
            $this->whereConditions,
            $this->whereValues
        );

        return $this->database->exec($query, $params);
    }

    public function delete(): PDOStatement
    {
        $queryBuilder = $this->getQueryBuilder();
        [$query, $params] = $queryBuilder->delete(
            $this->table,
            $this->whereConditions,
            $this->whereValues
        );

        return $this->database->exec($query, $params);
    }

    public static function now(): string
    {
        return date("Y-m-d H:i:s");
    }

    protected function getQueryBuilder(): object
    {
        if ($this->database->getType() == $this->database::MYSQL) {
            return new MySQLQueryBuilder();
        }

        throw new Exception(sprintf('Database engine: "%s" is not a valid engine', $this->database->getType()));
    }
}

?>