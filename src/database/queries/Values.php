<?php

namespace src\database\queries;

trait Values
{
    protected array $values = [];

    public function values(array $values = []): static
    {
        $this->values = $values;

        return $this;
    }
}

?>