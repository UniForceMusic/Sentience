<?php

namespace src\models\traits;

use src\exceptions\RelationException;
use src\models\relations\Relation;
use src\models\Model;

trait Relations
{
    protected array $relations = [];

    public function getRelation(string $name, callable $modifyQuery = null): null|Model|array
    {
        if (!key_exists($name, $this->relations)) {
            throw new RelationException(sprintf('relation with model %s does not exist', $name));
        }

        return $this->relations[$name]
            ->retrieve($this->database, $this, $modifyQuery);
    }

    protected function registerRelations(): void
    {
        /**
         * Override in model
         */
    }

    protected function registerRelation(Relation $relation, ?string $nameOverride = null): void
    {
        $relationName = $nameOverride ?? $relation->relationModel::getTable();

        $this->relations[$relationName] = $relation;
    }
}
