<?php

namespace src\models\relations;

use Closure;
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
            ->model($this->relationModel)
            ->where(
                $this->foreignKeyColumnName,
                Query::EQUALS,
                $model->getPrimaryKeyValue()
            );

        $query = $this->modifyQuery($query, $modifyQuery);

        return $query->selectModels();
    }
}
