<?php

namespace src\models\relations;

use Closure;

abstract class Relation implements RelationInterface
{
    public string $relationModel;
    public ?Closure $modifyDefaultQuery = null;
}
