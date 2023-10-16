<?php

namespace src\responses;

use src\models\Model;

abstract class Response implements ResponseInterface
{
    public static function fromAssoc(array $assoc): array
    {
        return $assoc;
    }

    public static function fromModel(Model $model, array $relations = []): array
    {
        $assoc = $model->exportAsRecord();

        foreach ($relations as $relation) {
            $relationModel = $model->getRelation($relation);
            if (!$relationModel) {
                continue;
            }

            $assoc[$relation] = $model->getRelation($relation)->exportAsRecord();
        }

        return $assoc;
    }

    public static function fromArrayOfAssocs(array $array): array
    {
        return array_map(
            function (array $el) {
                return static::fromAssoc($el);
            },
            $array
        );
    }

    public static function fromArrayOfModels(array $array, array $relations = []): array
    {
        return array_map(
            function (Model $model) use ($relations) {
                return static::fromModel($model, $relations);
            },
            $array
        );
    }
}
