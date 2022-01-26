<?php

namespace Pinnacle\OpenIdConnect\Support\Models;

use Assert\Assert;
use Pinnacle\OpenIdConnect\Support\Exceptions\EmptyStringException;

class NonEmptyString
{
    public function __construct(private string $value)
    {
        if (trim($value) === '') {
            throw new EmptyStringException(sprintf('%s was provided an empty string in the constructor.', self::class));
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
