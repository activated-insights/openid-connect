<?php

namespace Pinnacle\OpenIdConnect\Provider;

use Assert\Assert;

class Identifier
{
    public function __construct(private string|int $identifierValue)
    {
        if (is_string($this->identifierValue)) {
            Assert::that($this->identifierValue)->notBlank();
        }
    }

    public function getValue(): string|int
    {
        return $this->identifierValue;
    }
}
