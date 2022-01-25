<?php

namespace Pinnacle\OpenIdConnect\Support;

use Assert\Assert;

class NonEmptyString
{
    public function __construct(private string $value)
    {
        Assert::that($this->value)->notBlank();
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
