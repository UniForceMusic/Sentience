<?php

namespace src\requests;

use Exception;
use src\util\Strings;

abstract class Request implements RequestInterface
{
    protected array $payload;

    public function __construct(?array $payload)
    {
        if (!$payload) {
            throw new Exception('no payload detected');
        }

        $this->validateData($payload);
    }

    protected function validateEmail(array $payload): void
    {
        if (!isset($payload['email'])) {
            throw new Exception('email is required');
        }

        $emailValid = filter_var($payload['email'], FILTER_VALIDATE_EMAIL);

        if (!$emailValid) {
            throw new Exception('email invalid');
        }
    }

    protected function validatePassword(array $payload): void
    {
        if (!isset($payload['password'])) {
            throw new Exception('password is required');
        }

        $password = trim($payload['password']);

        if (strlen($password) < $_ENV['PASSWORD_MINIMUM_LENGTH']) {
            throw new Exception(sprintf('password must be %s characters or longer', $_ENV['PASSWORD_MINIMUM_LENGTH']));
        }

        if (strtolower($password) == $password && $_ENV['PASSWORD_CAPITAL_LETTER']) {
            throw new Exception('password must contain atleast 1 capital letter');
        }

        if (!preg_match('/\d/', $password) && $_ENV['PASSWORD_NUMBER']) {
            throw new Exception('password must contain atleast 1 digit');
        }

        if (preg_match('/^[\w&.-]+$/', $password) && $_ENV['PASSWORD_SPECIAL_CHARACTER']) {
            throw new Exception('password must contain atleast 1 special character');
        }
    }

    protected function validatePhone(array $payload): void
    {
        if (!isset($payload['phone'])) {
            throw new Exception('phone is required');
        }

        if (!preg_match('/^(\+\d{1,2}\s?)?1?\-?\.?\s?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/', Strings::strip(' ', $payload['phone']))) {
            throw new Exception('phone is invalid');
        }
    }

    protected function validateStringNotEmpty(?string $string, string $fieldName): void
    {
        if (!$string) {
            throw new Exception(sprintf('%s must be set', strtolower($fieldName)));
        }

        if (empty($string)) {
            throw new Exception(sprintf('%s cannot be empty', strtolower($fieldName)));
        }
    }

    protected function validateNotZero(int|float $number, string $fieldName): void
    {
        if ($number == 0 || $number < 0) {
            throw new Exception(sprintf('%s cannot be empty', strtolower($fieldName)));
        }
    }

    protected function validateInRange(int|float $number, string $fieldName, float $min, float $max): void
    {
        if ($number <= $min || $number >= $max) {
            throw new Exception(sprintf('%s is not between %s and %s', strtolower($fieldName), $min, $max));
        }
    }

    protected function isArray(array $array): bool
    {
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
