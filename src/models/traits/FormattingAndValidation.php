<?php

namespace src\models\traits;

use ReflectionProperty;
use Throwable;
use src\exceptions\ValidationException;

trait FormattingAndValidation
{
    protected array $onSave = [];

    public function testValidate(bool $runOnInsertOrUpdateFirst = false): ?string
    {
        /**
         * Returns null if no error is detected
         * 
         * Returns the error message as a string if the validate data function failed
         * 
         * If a generic false is returned the method return 'data invalid'
         */

        if ($runOnInsertOrUpdateFirst) {
            $this->onInsertOrUpdate();
        }

        try {
            $valid = $this->validate();

            if (!$valid) {
                throw new ValidationException(
                    sprintf(
                        'data invalid for model: %s',
                        ($this::class)
                    )
                );
            }

            return null;
        } catch (Throwable $err) {
            return $err->getMessage();
        }
    }

    protected function validate(): bool
    {
        /**
         * If the data is valid return true
         * 
         * If the data is invalid you have two options:
         * 1) Return false to silently let the process fail
         * 2) Throw an exception to notify the user something went wrong
         * 
         * Tip:
         * You can correct data on the fly in this method, but it is recommended
         * to use onInsertOrUpdate for that
         */

        return true;
    }

    public function formatProperties(): static
    {
        foreach ($this->columns as $propertyName => $columnName) {
            if (!(new ReflectionProperty($this, $propertyName))->isInitialized($this)) {
                continue;
            }

            $this->formatProperty($propertyName);
        }

        return $this;
    }

    protected function formatProperty(string $propertyName): void
    {
        $value = $this->{$propertyName};

        $onSaveFunc = $this->onSave[$propertyName] ?? null;
        if (!$onSaveFunc) {
            return;
        }

        if (is_array($onSaveFunc)) {
            $this->{$propertyName} = $onSaveFunc($value);
            return;
        }

        if (method_exists($this, $onSaveFunc)) {
            $this->{$propertyName} = $this->{$onSaveFunc}($value);
            return;
        }

        if (function_exists($onSaveFunc)) {
            $this->{$propertyName} = $onSaveFunc($value);
            return;
        }
    }
}
