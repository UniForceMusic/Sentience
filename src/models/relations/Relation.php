<?php

namespace src\models\relations;

use Closure;
use src\database\queries\Query;

abstract class Relation implements RelationInterface
{
    public string $relationModel;
    public ?Closure $modifyDefaultQuery = null;

    protected function modifyQuery(Query $query, ?callable $modifyQuery): Query
    {
        if ($this->modifyDefaultQuery) {
            $modifyDefaultQuery = $this->modifyDefaultQuery;
            $query = $modifyDefaultQuery($query);
        }

        if ($modifyQuery) {
            $query = $modifyQuery($query);
        }

        return $query;
    }
}
