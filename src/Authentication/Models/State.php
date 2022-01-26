<?php

namespace Pinnacle\OpenIdConnect\Authentication\Models;

use Pinnacle\OpenIdConnect\Support\Models\NonEmptyString;
use Pinnacle\OpenIdConnect\Support\Traits\RandomStringGenerationTrait;

class State extends NonEmptyString
{
    use RandomStringGenerationTrait;

    public static function createWithRandomString():self
    {
        return new self(self::generateRandomString());
    }
}
