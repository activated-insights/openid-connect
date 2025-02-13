<?php

namespace Pinnacle\OpenIdConnect\Support\Models;

use Pinnacle\OpenIdConnect\Support\Exceptions\EmptyStringException;

class FilledString
{
    public function __construct(protected string $value)
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
