<?php

namespace src\database\queries;

trait Properties
{
    protected array $properties = [];

    public function properties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }
}

?>