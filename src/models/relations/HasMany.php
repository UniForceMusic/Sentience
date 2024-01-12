<?php

namespace src\models\relations;

use Closure;
use PDO;
use src\database\Database;
use src\database\queries\Query;
use src\models\Model;

class HasMany extends Relation implements RelationInterface
{
    protected string $foreignKeyColumnName;

    public function __construct(string $relationModel, string $foreignKeyColumnName, callable|Closure $modifyDefaultQuery = null)
    {
        $this->relationModel = $relationModel;
        $this->foreignKeyColumnName = $foreignKeyColumnName;
        $this->modifyDefaultQuery = $modifyDefaultQuery;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): array
    {
        $query = $database->query()
            ->table($this->relationModel::getTable())
            ->where(
                $this->foreignKeyColumnName,
                Query::EQUALS,
                $model->getPrimaryKeyValue()
            );

        if ($this->modifyDefaultQuery) {
            $modifyDefaultQuery = $this->modifyDefaultQuery;
            $query = $modifyDefaultQuery($query);
        }

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        $statement = $query->select();

        $relationModels = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $assoc) {
            $relationModel = $this->relationModel;
            $relationModels[] = (new $relationModel($database))->hydrateByAssoc($statement, $assoc);
        }

        return $relationModels;
    }
}
