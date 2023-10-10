<?php

namespace src\models\relations;

use PDO;
use src\database\Database;
use src\database\queries\Query;
use src\models\Model;

class HasMany extends Relation implements RelationInterface
{
    protected string $foreignKeyColumnName;

    public function __construct(string $relationModel, string $foreignKeyColumnName)
    {
        $this->relationModel = $relationModel;
        $this->foreignKeyColumnName = $foreignKeyColumnName;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): array
    {
        $query = $database->query()
            ->table($this->relationModel::getTable())
            ->where($this->foreignKeyColumnName, Query::EQUALS, $model->getPrimaryKeyValue());

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        $statement = $query->select();

        $relationModels = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $assoc) {
            $model = new $this->relationModel($database);
            $relationModels[] = $model->hydrateByAssoc($statement, $assoc);
        }

        return $relationModels;
    }
}
