<?php

namespace src\database\queries;

trait Table
{
    protected string $table = '';

    public function table(string $table): static
    {
        $this->table = $table;

        return $this;
    }
}

?>