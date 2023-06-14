<?php

namespace src\database\queries;

trait Limit
{
    protected int $limit = 0;

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }
}

?>