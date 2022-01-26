<?php

namespace Pinnacle\OpenIdConnect\Provider\Models;

use Pinnacle\OpenIdConnect\Support\Exceptions\EmptyStringException;

class Identifier
{
    public function __construct(private string|int $identifierValue)
    {
        if (is_string($this->identifierValue) && trim($this->identifierValue) === '') {
            throw new EmptyStringException(sprintf('%s was provided an empty string in the constructor.', self::class));
        }
    }

    public function getValue(): string|int
    {
        return $this->identifierValue;
    }
}
