<?php

namespace src\responses;

use src\models\Model;

abstract class Response implements ResponseInterface
{
    public static function fromAssoc(array $assoc): array
    {
        return $assoc;
    }

    public static function fromModel(Model $model): array
    {
        return (array) $model;
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

    public static function fromArrayOfModels(array $array): array
    {
        return array_map(
            function (Model $model) {
                return static::fromModel($model);
            },
            $array
        );
    }
}
