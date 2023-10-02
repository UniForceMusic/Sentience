<?php

namespace src\database\queries;

trait Offset
{
    protected ?int $offset = null;

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }
}
