<?php

namespace src\csv;

use DateTime;
use src\exceptions\FileException;
use src\util\Strings;

class Csv
{
    public const DEFAULT_DELIMITER = ',';

    protected string $newLine = PHP_EOL;
    protected string $delimiter = ',';
    protected array $keys = [];
    protected array $rows = [];

    public static function parseFromFile(string $filePath, ?string $delimiter = CSV::DEFAULT_DELIMITER): static
    {
        if (!file_exists($filePath)) {
            throw new FileException('Csv file path is invalid');
        }

        $csvString = file_get_contents($filePath);

        return new static($csvString, $delimiter);
    }

    public static function parseFromString(string $string, ?string $delimiter = CSV::DEFAULT_DELIMITER): static
    {
        return new static($string, $delimiter);
    }

    public static function parseFromArrayOfObjects(array $arrayOfObjects): static
    {
        $csv = new static();

        foreach ($arrayOfObjects as $index => $object) {
            $csv->addRow(
                get_object_vars($object)
            );
        }

        return $csv;
    }

    public static function createEmpty(): static
    {
        return new static();
    }

    public function __construct(string $string = '', string $delimiter = null)
    {
        if (empty($string)) {
            return;
        }

        $newLine = Strings::detectNewline($string);
        $delimiter = $delimiter ?? $this->detectDelimiter($string, $newLine);

        $lines = $this->removeSeparatorFromString(
            explode($newLine, $string)
        );

        $keys = $this->split($lines[0], $delimiter);

        $rows = (count($lines) > 1)
            ? array_slice($lines, 1)
            : [];

        foreach ($rows as $index => $values) {
            $rows[$index] = $this->matchValuesToKeys(
                $keys,
                $this->split($values, $delimiter)
            );
        }

        $this->newLine = $newLine;
        $this->delimiter = $delimiter;
        $this->keys = $keys;
        $this->rows = $rows;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter($delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function setKeys(array $keys): static
    {
        $this->keys = $keys;

        return $this;
    }

    public function addKey(int|float|string $key): static
    {
        $this->keys[] = $key;

        return $this;
    }

    public function removeKey(int|float|string $key): static
    {
        foreach ($this->keys as $i => $k) {
            if ($k == $key) {
                unset($this->keys[$i]);
            }
        }

        $this->keys = array_values($this->keys);

        foreach ($this->rows as $index => $row) {
            unset($this->rows[$index][$key]);
        }

        return $this;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): static
    {
        foreach ($rows as $row) {
            foreach ($row as $key => $value) {
                if (!in_array($key, $this->keys)) {
                    $this->addKey($key);
                }
            }
        }

        $this->rows = $rows;

        return $this;
    }

    public function addRow(array $values): static
    {
        foreach ($values as $key => $value) {
            if (!in_array($key, $this->keys)) {
                $this->addKey($key);
            }
        }

        $this->rows[] = $values;

        return $this;
    }

    public function writeToString(): string
    {
        $keys = $this->stringifyKeys($this->newLine, $this->delimiter);
        $rows = $this->stringifyRows($this->newLine, $this->delimiter);

        if ($this->delimiter !== static::DEFAULT_DELIMITER) {
            return $this->joinStrings(
                [
                    sprintf('sep=%s', $this->delimiter),
                    $keys,
                    $rows
                ],
                $this->newLine
            );
        }

        return $this->joinStrings(
            [
                $keys,
                $rows,
            ],
            $this->newLine
        );
    }

    public function writeToFile(string $filePath): static
    {
        file_put_contents($filePath, $this->writeToString());

        return $this;
    }

    protected function matchValuesToKeys(array $keys, array $values): array
    {
        $array = [];

        foreach ($keys as $index => $key) {
            if (isset($values[$index])) {
                $array[$key] = $values[$index];
            } else {
                $array[$key] = '';
            }
        }

        return $array;
    }

    protected function detectDelimiter(string $string, string $newLine): string
    {
        $lines = $this->split($string, $newLine);

        if (str_contains($lines[0], 'sep=')) {
            preg_match('/sep=(.[^\)]*)/', $lines[0], $matches);

            return $matches[1];
        }

        return $this->delimiter;
    }

    protected function removeSeparatorFromString(array $lines): array
    {
        if (!$lines) {
            return $lines;
        }

        if (str_contains($lines[0], 'sep=')) {
            preg_match('/sep=(.[^\)]*)/', $lines[0], $matches);

            return array_slice($lines, 1);
        }

        return $lines;
    }

    protected function split(string $string, string $separator): array
    {
        return str_getcsv($string, $separator);
    }

    protected function stringifyKeys(string $newLine, string $delimiter): string
    {
        return $this->join(
            $this->keys,
            $newLine,
            $delimiter
        );
    }

    protected function stringifyRows(string $newLine, string $delimiter): string
    {
        $rowStrings = [];

        foreach ($this->rows as $values) {
            $rowStrings[] = $this->join(
                $this->matchKeyOrder($this->keys, $values),
                $newLine,
                $delimiter
            );
        }

        return $this->joinStrings($rowStrings, $newLine);
    }

    protected function matchKeyOrder(array $keys, array $values): array
    {
        $sortedValues = [];

        /**
         * Add missing keys to values array so missing keys don't mess up the order
         */
        foreach ($keys as $key) {
            if (!key_exists($key, $values)) {
                $values[$key] = null;
            }
        }

        /**
         * Create an array that has the keys sorted numerically like this:
         * '0' => ['key', 'value']
         * '1' => ['key', 'value']
         * 
         * Then it turns them back into an assosiative array with the key and value from the array
         */
        foreach ($values as $key => $value) {
            $index = array_search($key, $keys);

            $sortedValues[$index] = [$key, $value];
        }

        ksort($sortedValues);

        $sortedKeyValuePairs = [];

        foreach ($sortedValues as $valuePair) {
            $key = $valuePair[0];
            $value = $valuePair[1];
            $sortedKeyValuePairs[$key] = $value;
        }

        return $sortedKeyValuePairs;
    }

    protected function joinStrings(array $strings, string $newLine): string
    {
        return implode($newLine, $strings);
    }

    protected function join(array $items, string $newLine, string $delimiter): string
    {
        $serializedItems = array_map(function ($item) use ($newLine, $delimiter) {
            if (is_null($item)) {
                return '';
            }

            if ($item instanceof DateTime) {
                $item = $item->format('Y-m-d H:i:s');
            }

            $type = gettype($item);
            if (in_array($type, ['array', 'object'])) {
                $item = sprintf('<unsupported content type: %s>', $type);
            }

            if (
                in_array(
                    true,
                    [
                        str_contains($item, $newLine),
                        str_contains($item, $delimiter)
                    ]
                )
            ) {
                return sprintf('"%s"', str_replace('"', '""', $item));
            }

            return (string) $item;
        }, $items);

        return implode($delimiter, $serializedItems);
    }

    public function filter($callback = null): static
    {
        $rows = array_filter(
            $this->rows,
            $callback
        );

        $this->rows = array_values($rows);

        return $this;
    }

    public function map(callable $callback): static
    {
        $rows = $this->rows;

        foreach ($rows as $index => $values) {
            $rows[$index] = $callback($values, $index);
        }

        $this->rows = array_values($rows);

        return $this;
    }
}
