<?php

namespace src\models;

use src\database\Database;
use src\models\traits\CreateReadUpdateDelete as CreateReadUpdateDeleteTrait;
use src\models\traits\Database as DatabaseTrait;
use src\models\traits\FormattingAndValidation as FormattingAndValidationTrait;
use src\models\traits\PrimaryKey as PrimaryKeyTrait;
use src\models\traits\Relations as RelationsTrait;
use src\models\traits\Table as TableTrait;
use src\models\traits\Unique as UniqueTrait;

abstract class Model
{
    use CreateReadUpdateDeleteTrait;
    use DatabaseTrait;
    use FormattingAndValidationTrait;
    use PrimaryKeyTrait;
    use RelationsTrait;
    use TableTrait;
    use UniqueTrait;

    public function __construct(Database $database)
    {
        $this->database = $database;

        $this->registerRelations();
    }

    public function __toString(): string
    {
        return (string) $this->getPrimaryKeyValue();
    }
}
