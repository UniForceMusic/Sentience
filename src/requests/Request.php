<?php

namespace src\requests;

use Exception;
use src\app\Request as IncomingRequest;
use src\util\Strings;

abstract class Request implements RequestInterface
{
    public function __construct(IncomingRequest $request, mixed $parsedPayload = null)
    {
        $this->validateAndHydrate($request, $parsedPayload);
    }

    protected function validateEmail(?string $email, string $fieldName): void
    {
        if (!$email) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$emailValid) {
            throw new Exception(sprintf('%s is invalid', $fieldName));
        }
    }

    protected function validatePassword(?string $password, string $fieldName): void
    {
        if (!$password) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        $password = trim($password);

        if (strlen($password) < $_ENV['PASSWORD_MINIMUM_LENGTH']) {
            throw new Exception(sprintf('%s must be %s characters or longer', $fieldName, $_ENV['PASSWORD_MINIMUM_LENGTH']));
        }

        if (strtolower($password) == $password && $_ENV['PASSWORD_CAPITAL_LETTER']) {
            throw new Exception(sprintf('%s must contain atleast 1 capital letter', $fieldName));
        }

        if (!preg_match('/\d/', $password) && $_ENV['PASSWORD_NUMBER']) {
            throw new Exception(sprintf('%s must contain atleast 1 digit', $fieldName));
        }

        if (preg_match('/^[\w&.-]+$/', $password) && $_ENV['PASSWORD_SPECIAL_CHARACTER']) {
            throw new Exception(sprintf('%s must contain atleast 1 special character', $fieldName));
        }
    }

    protected function validatePhone(?string $phone, string $fieldName): void
    {
        if (!$phone) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        if (!preg_match('/^(\+\d{1,2}\s?)?1?\-?\.?\s?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/', Strings::strip(' ', $phone))) {
            throw new Exception(sprintf('%s is invalid', $fieldName));
        }
    }

    protected function validateStringNotEmpty(?string $string, string $fieldName): void
    {
        if (!$string) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        if (empty($string)) {
            throw new Exception(sprintf('%s cannot be empty', $fieldName));
        }
    }

    protected function validateNotZero(null|int|float $number, string $fieldName): void
    {
        if (!$number) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        if ($number <= 0) {
            throw new Exception(sprintf('%s cannot be zero', $fieldName));
        }
    }

    protected function validateInRange(null|int|float $number, string $fieldName, float $min, float $max): void
    {
        if (!$number) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        if ($number <= $min || $number >= $max) {
            throw new Exception(sprintf('%s is not between %s and %s', $fieldName, $min, $max));
        }
    }

    protected function isArray(?array $array, string $fieldName): bool
    {
        if (is_null($array)) {
            throw new Exception(sprintf('%s must be set', $fieldName));
        }

        $counter = 0;

        foreach ($array as $key => $value) {
            if ($key != strval($counter)) {
                return false;
            }

            $counter++;
        }

        return true;
    }
}
