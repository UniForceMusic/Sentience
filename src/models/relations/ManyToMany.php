<?php

namespace src\models\relations;

use Closure;
use PDO;
use src\database\Database;
use src\database\objects\Join;
use src\database\queries\Query;
use src\database\querybuilders\QueryBuilderInterface;
use src\models\Model;

class ManyToMany extends Relation implements RelationInterface
{
    protected string $junctionTableModel;
    protected string $junctionTableParentColumnName;
    protected string $junctionTableRelationColumnName;

    public function __construct(string $relationModel, string $junctionTableModel, string $junctionTableParentColumnName, string $junctionTableRelationColumnName, callable|Closure $modifyDefaultQuery = null)
    {
        $this->relationModel = $relationModel;
        $this->junctionTableModel = $junctionTableModel;
        $this->junctionTableParentColumnName = $junctionTableParentColumnName;
        $this->junctionTableRelationColumnName = $junctionTableRelationColumnName;
        $this->modifyDefaultQuery = $modifyDefaultQuery;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): array
    {
        $queryBuilder = $database->getQueryBuilder();

        $relationModel = new $this->relationModel($database);

        $query = $database->query()
            ->model($this->junctionTableModel)
            ->join(
                Join::LEFT_JOIN,
                $relationModel::getTable(),
                $this->junctionTableRelationColumnName,
                $relationModel::getPrimaryKeyColumnName()
            )
            ->where(
                $queryBuilder->getColumnWithNamespace(
                    $this->junctionTableModel::getTable(),
                    $this->junctionTableParentColumnName,
                    true
                ),
                Query::EQUALS,
                $model->getPrimaryKeyValue(),
                false
            );

        $query = $this->modifyQuery($query, $modifyQuery);

        return $query->selectModels();
    }
}
