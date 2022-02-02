<?php

namespace Pinnacle\OpenIdConnect\Tests\Traits;

use Firebase\JWT\JWT;

trait GenerateUserIdJwt
{
    private function generateRandomJwt(): string
    {
        return $this->generateJwtWithRequiredValues(
            'https://test.dev',
            'subject-identifier',
            'audience',
            time() + 60,
            time()
        );
    }

    private function generateJwtWithRequiredValues(
        string $issuerIdentifier,
        string $subjectIdentifier,
        string|array $audiences,
        int $expirationTime,
        int $issuedTime
    ): string {
        return $this->generateJwtWithPayloadValues(
            [
                'iss' => $issuerIdentifier,
                'sub' => $subjectIdentifier,
                'aud' => $audiences,
                'exp' => $expirationTime,
                'iat' => $issuedTime,
            ]
        );
    }

    private function generateJwtWithPayloadValues(array $payloadValues): string
    {
        return JWT::encode($payloadValues, 'fake-key', 'HS256');
    }
}
