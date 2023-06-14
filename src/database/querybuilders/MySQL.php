<?php

namespace src\database\querybuilders;

class MySQL
{
    public function select(string $table, array $columns, array $whereConditions, array $whereValues, int $limit): array
    {
        $query = '';
        $params = [];

        $query .= 'SELECT ' . $this->generateColumnsString($columns) . ' FROM `' . $table . '` ';

        if (!empty($whereConditions)) {
            $query .= 'WHERE ' . $this->generateWhereString($whereConditions) . ' ';
            array_push($params, ...$whereValues);
        }

        if ($limit > 0) {
            $query .= 'LIMIT ' . $limit;
        }

        $query = trim($query) . ';';

        return [$query, $params];
    }

    public function insert(string $table, array $values): array
    {
        $query = '';
        $params = array_values($values);

        $query .= 'INSERT INTO `' . $table . '` (' . $this->generateColumnsString(array_keys($values)) . ') VALUES (' . $this->generatePlaceholdersString(count($values)) . ')';

        $query = trim($query) . ';';

        return [$query, $params];
    }

    public function update(string $table, array $values, array $whereConditions, array $whereValues): array
    {
        $query = '';
        $params = [];

        $query .= 'UPDATE `' . $table . '` SET ' . $this->generateUpdateString($values) . ' ';
        array_push($params, ...array_values($values));

        if (!empty($whereConditions)) {
            $query .= 'WHERE ' . $this->generateWhereString($whereConditions) . ' ';
            array_push($params, ...$whereValues);
        }

        $query = trim($query) . ';';

        return [$query, $params];
    }

    public function delete(string $table, array $whereConditions, array $whereValues): array
    {
        $query = '';
        $params = [];

        $query .= 'DELETE FROM `' . $table . '` ';

        if (!empty($whereConditions)) {
            $query .= 'WHERE ' . $this->generateWhereString($whereConditions) . ' ';
            array_push($params, ...$whereValues);
        }

        return [$query, $params];
    }

    protected function generateColumnsString(array $columns): string
    {
        if (empty($columns)) {
            return '*';
        }

        $escapedColumns = array_map(
            function (string $column): string {
                return sprintf('`%s`', $column);
            },
            $columns
        );

        return implode(', ', $escapedColumns);
    }

    protected function generateWhereString(array $whereConditions): string
    {
        $conditions = [];

        foreach ($whereConditions as $where) {
            if (in_array($where, ['AND', 'OR'])) {
                $conditions[] = $where;
                continue;
            }

            $conditions[] = sprintf(
                '`%s` %s ?',
                $where->key,
                $where->comparator
            );
        }

        return implode(' ', $conditions);
    }

    protected function generatePlaceholdersString(int $size): string
    {
        $placeholders = [];

        for ($i = 0; $i < $size; $i++) {
            $placeholders[] = '?';
        }

        return implode(', ', $placeholders);
    }

    protected function generateUpdateString(array $values): string
    {
        $updates = [];

        foreach ($values as $key => $value) {
            $updates[] = sprintf(
                '`%s` = ?',
                $key
            );
        }

        return implode(', ', $updates);
    }
}

?>