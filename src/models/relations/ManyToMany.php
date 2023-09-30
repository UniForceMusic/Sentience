<?php

namespace src\models\relations;

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

    public function __construct(string $relationModel, string $junctionTableModel, string $junctionTableParentColumnName, string $junctionTableRelationColumnName)
    {
        $this->relationModel = $relationModel;
        $this->junctionTableModel = $junctionTableModel;
        $this->junctionTableParentColumnName = $junctionTableParentColumnName;
        $this->junctionTableRelationColumnName = $junctionTableRelationColumnName;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): array
    {
        $queryBuilder = $database->getQueryBuilder();

        $relationModelName = $this->relationModel;
        $relationModel = new $relationModelName($database);

        $junctionModelName = $this->junctionTableModel;
        $junctionModel = new $junctionModelName($database);

        $query = $database->query()
            ->table($junctionModel::getTable())
            ->columns(
                $this->getColumnsWithNamespace(
                    $queryBuilder,
                    $relationModel::getTable(),
                    $relationModel->getColumns()
                )
            )
            ->join(
                Join::LEFT_JOIN,
                $relationModel::getTable(),
                $this->junctionTableRelationColumnName,
                $relationModel->getPrimaryKeyColumnName()
            )
            ->where(
                $queryBuilder->getColumnWithNamespace($junctionModel::getTable(), $this->junctionTableParentColumnName),
                Query::EQUALS,
                $model->getPrimaryKeyValue(),
                true
            );

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        $statement = $query->select();

        if ($statement->rowCount() < 1) {
            return [];
        }

        $relationModels = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $assoc) {
            $relationModel = new $relationModelName($database);
            $relationModels[] = $relationModel->hydrateByArray($statement, $assoc);
        }

        return $relationModels;
    }

    protected function getColumnsWithNamespace(QueryBuilderInterface $queryBuilder, string $table, array $columns): array
    {
        return array_map(
            function (string $column) use ($table, $queryBuilder) {
                return $queryBuilder->getColumnWithNamespace($table, $column);
            },
            $columns
        );
    }
}
