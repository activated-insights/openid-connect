<?php

namespace Pinnacle\OpenIdConnect\Authentication\Models;

use Pinnacle\OpenIdConnect\Support\Models\FilledString;
use Pinnacle\OpenIdConnect\Support\Traits\RandomStringGenerationTrait;

class State extends FilledString
{
    use RandomStringGenerationTrait;

    public static function createWithRandomString(): self
    {
        return new self(self::generateRandomString());
    }
}
