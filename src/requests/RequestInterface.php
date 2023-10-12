<?php

namespace src\requests;

use src\app\Request as IncomingRequest;

interface RequestInterface
{
    public function validateAndHydrate(IncomingRequest $request, mixed $parsedPayload): void;
}
