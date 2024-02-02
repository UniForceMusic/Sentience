<?php

namespace src\csv;

class CsvLowMemory
{
    protected array $keys;
    protected string $delimiter;
    protected string $tmp;

    public function __construct(array $keys, string $delimiter = ',')
    {
        $this->keys = $keys;
        $this->delimiter = $delimiter;
        $this->tmp = $this->createTmpFilePath();

        $this->writeSingleLineToFile($keys, false);
    }

    public function addRow(array $values): void
    {
        $this->writeSingleLineToFile(
            $this->matchKeyOrder($values)
        );
    }

    public function addRows(array $rows): void
    {
        $this->writeMultipleLinesToFile(
            array_map(
                function (array $row) {
                    return $this->matchKeyOrder($row);
                },
                $rows
            )
        );
    }

    public function readClose(): string
    {
        $contents = sprintf(
            '%s%s',
            file_get_contents($this->tmp),
            PHP_EOL
        );

        unlink($this->tmp);

        return $contents;
    }

    protected function matchKeyOrder(array $values): array
    {
        $orderedValues = [];

        foreach ($this->keys as $key) {
            if (!key_exists($key, $values)) {
                $orderedValues[$key] = null;
                continue;
            }

            $orderedValues[$key] = $values[$key];
        }

        return $orderedValues;
    }

    protected function writeSingleLineToFile(array $values, bool $includeNewline = true): void
    {
        $file = fopen($this->tmp, 'a');

        fwrite(
            $file,
            sprintf(
                '%s%s',
                ($includeNewline) ? PHP_EOL : '',
                implode(
                    $this->delimiter,
                    $values
                )
            )
        );

        fclose($file);
    }

    protected function writeMultipleLinesToFile(array $rows, bool $includeNewline = true): void
    {
        $file = fopen($this->tmp, 'a');

        foreach ($rows as $row) {
            fwrite(
                $file,
                sprintf(
                    '%s%s',
                    ($includeNewline) ? PHP_EOL : '',
                    implode(
                        $this->delimiter,
                        $row
                    )
                )
            );
        }

        fclose($file);
    }

    protected function createTmpFilePath(): string
    {
        $tempDir = sys_get_temp_dir();

        while (true) {
            $tmpFilePath = appendToBaseDir(
                $tempDir,
                sprintf(
                    '%s.csv',
                    md5(uniqid())
                )
            );

            if (!file_exists($tmpFilePath)) {
                return $tmpFilePath;
            }
        }
    }
}
