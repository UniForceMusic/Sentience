<?php

namespace src\models\relations;

use PDO;
use src\database\Database;
use src\database\queries\Query;
use src\models\Model;

class BelongsTo extends Relation implements RelationInterface
{
    protected string $foreignKeyColumnName;

    public function __construct(string $relationModel, string $foreignKeyColumnName)
    {
        $this->relationModel = $relationModel;
        $this->foreignKeyColumnName = $foreignKeyColumnName;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): ?Model
    {
        $relationModelName = $this->relationModel;
        $relationModel = new $relationModelName($database);

        $query = $database->query()
            ->table($relationModel::getTable())
            ->where(
                $this->foreignKeyColumnName,
                Query::EQUALS,
                $model->getPrimaryKeyValue()
            )
            ->limit(1);

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        $statement = $query->select();


        if ($statement->rowCount() < 1) {
            return null;
        }

        return $relationModel->hydrateByArray(
            $statement,
            $statement->fetch(PDO::FETCH_ASSOC)
        );
    }
}
