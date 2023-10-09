<?php

namespace src\database\queries;

trait Limit
{
    protected ?int $limit = null;

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }
}
