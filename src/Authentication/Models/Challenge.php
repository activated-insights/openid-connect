<?php

namespace Pinnacle\OpenIdConnect\Authentication\Models;

use Pinnacle\OpenIdConnect\Support\Models\FilledString;
use Pinnacle\OpenIdConnect\Support\Traits\RandomStringGenerationTrait;

class Challenge extends FilledString
{
    use RandomStringGenerationTrait;

    public function hash(): string
    {
        $binaryHash    = hash('sha256', $this->value, true);
        $base64Encoded = base64_encode($binaryHash);

        // Convert from standard Base64 encoding to Base64Url encoding.
        return rtrim(strtr($base64Encoded, '+/', '-_'), '=');
    }

    public static function createWithRandomString(): self
    {
        return new self(self::generateRandomString(64));
    }
}
