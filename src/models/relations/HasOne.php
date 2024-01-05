<?php

namespace src\models\relations;

use Closure;
use PDO;
use src\database\Database;
use src\database\queries\Query;
use src\models\Model;

class HasOne extends Relation implements RelationInterface
{
    protected string $foreignKeyColumnName;

    public function __construct(string $relationModel, string $foreignKeyColumnName, callable|Closure $modifyDefaultQuery = null)
    {
        $this->relationModel = $relationModel;
        $this->foreignKeyColumnName = $foreignKeyColumnName;
        $this->modifyDefaultQuery = $modifyDefaultQuery;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): ?Model
    {
        $relationModel = new $this->relationModel($database);

        $query = $database->query()
            ->table($relationModel::getTable())
            ->where(
                $relationModel->getPrimaryKeyColumnName(),
                Query::EQUALS,
                $model->exportAsRecord()[$this->foreignKeyColumnName]
            )
            ->limit(1);

        if ($this->modifyDefaultQuery) {
            $modifyDefaultQuery = $this->modifyDefaultQuery;
            $query = $modifyDefaultQuery($query);
        }

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        $statement = $query->select();

        if ($statement->rowCount() < 1) {
            return null;
        }

        return $relationModel->hydrateByAssoc(
            $statement,
            $statement->fetch(PDO::FETCH_ASSOC)
        );
    }
}
