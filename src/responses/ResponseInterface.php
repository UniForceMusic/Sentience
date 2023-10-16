<?php

namespace src\responses;

use src\models\Model;

interface ResponseInterface
{
    public static function fromAssoc(array $assoc): array;

    public static function fromModel(Model $model, array $relations = []): array;
}
