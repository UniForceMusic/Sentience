<?php

namespace src\models\relations;

use PDO;
use src\database\Database;
use src\database\queries\Query;
use src\models\Model;

class ManyToMany extends Relation implements RelationInterface
{
    protected string $junctionTableModel;
    protected int|string $parentPKColumnName;
    protected int|string $relationPKColumnName;

    public function __construct(string $relationModel, string $junctionTableModel, string $parentPKColumnName, string $relationPKColumnName)
    {
        $this->relationModel = $relationModel;
        $this->junctionTableModel = $junctionTableModel;
        $this->parentPKColumnName = $parentPKColumnName;
        $this->relationPKColumnName = $relationPKColumnName;
    }

    public function retrieve(Database $database, Model $model, callable $modifyQuery = null): array
    {
        $query = $database->query()
            ->table($this->junctionTableModel::getTable())
            ->where($this->parentPKColumnName, Query::EQUALS, $model->getPrimaryKeyValue());

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        $relations = $query->selectAssoc();

        if (!$relations) {
            return [];
        }

        $relationPKValues = array_map(
            function (array $row) {
                return $row[$this->relationPKColumnName];
            },
            $relations
        );

        $relationModelName = $this->relationModel;
        $relationModel = new $relationModelName($database);

        $statement = $database->query()
            ->table($relationModel::getTable())
            ->where($relationModel->getPrimaryKeyColumnName(), Query::IN_ARRAY, $relationPKValues)
            ->select();

        $relationModels = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $assoc) {
            $model = new $relationModelName($database);
            $relationModels[] = $model->hydrateByArray($statement, $assoc);
        }

        return $relationModels;
    }
}
