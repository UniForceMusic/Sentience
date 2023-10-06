<?php

namespace src\database\queries;

trait IfNotExists
{
    protected bool $ifNotExists = false;

    public function ifNotExists($ifNotExists = true): static
    {
        $this->ifNotExists = $ifNotExists;

        return $this;
    }
}
