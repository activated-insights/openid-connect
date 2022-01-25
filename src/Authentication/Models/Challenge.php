<?php

namespace Pinnacle\OpenIdConnect\Authentication\Models;

use Pinnacle\OpenIdConnect\Support\Models\NonEmptyString;
use Pinnacle\OpenIdConnect\Support\Traits\RandomStringGenerationTrait;

class Challenge extends NonEmptyString
{
    use RandomStringGenerationTrait;

    public static function createWithRandomChallenge(): self
    {
        return new self(self::generateCodeChallenge());
    }

    public function equals(Challenge $challenge): bool
    {
        return $this->getValue() === $challenge->getValue();
    }

    private static function generateCodeChallenge(): string
    {
        $randomString  = self::generateRandomString(64);
        $binaryHash    = hash('sha256', $randomString, true);
        $base64Encoded = base64_encode($binaryHash);

        // Convert from standard Base64 encoding to Base64Url encoding.
        return rtrim(strtr($base64Encoded, '+/', '-_'), '=');
    }
}
