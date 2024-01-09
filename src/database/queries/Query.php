<?php

namespace src\database\queries;

use DateTime;
use src\database\Database;
use src\database\queries\Columns as ColumnsTrait;
use src\database\queries\Delete as DeleteTrait;
use src\database\queries\IfNotExists as IfNotExistsTrait;
use src\database\queries\Insert as InsertTrait;
use src\database\queries\Join as JoinTrait;
use src\database\queries\Limit as LimitTrait;
use src\database\queries\Model as ModelTrait;
use src\database\queries\Offset as OffsetTrait;
use src\database\queries\OrderBy as OrderByTrait;
use src\database\queries\PrimaryKey as PrimaryKeyTrait;
use src\database\queries\Properties as PropertiesTrait;
use src\database\queries\Select as SelectTrait;
use src\database\queries\Table as TableTrait;
use src\database\queries\Update as UpdateTrait;
use src\database\queries\Values as ValuesTrait;
use src\database\queries\Where as WhereTrait;

class Query
{
    public const EQUALS = '=';
    public const NOT_EQUALS = '!=';
    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
    public const IN_ARRAY = 'IN ARRAY';
    public const NOT_IN_ARRAY = 'NOT IN ARRAY';
    public const LESS_THAN = '<';
    public const MORE_THAN = '>';
    public const LESS_THAN_OR_EQUALS = '<=';
    public const MORE_THAN_OR_EQUALS = '>=';

    use ColumnsTrait;
    use DeleteTrait;
    use IfNotExistsTrait;
    use InsertTrait;
    use JoinTrait;
    use LimitTrait;
    use ModelTrait;
    use OffsetTrait;
    use OrderByTrait;
    use PrimaryKeyTrait;
    use PropertiesTrait;
    use SelectTrait;
    use TableTrait;
    use UpdateTrait;
    use ValuesTrait;
    use WhereTrait;

    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public static function now(): DateTime
    {
        return new DateTime();
    }

    public static function wildcard(string $substr): string
    {
        $substr = str_replace('%', '\%', $substr);
        $substr = str_replace('_', '\_', $substr);
        $substr = str_replace('[', '\[', $substr);
        $substr = str_replace('-', '\-', $substr);

        return ('%' . $substr . '%');
    }
}
