<?php

namespace src\requests;

interface RequestInterface
{
    public function validateData(array $payload): void;
}
