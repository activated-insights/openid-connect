<?php

namespace Pinnacle\OpenIdConnect\Tests\Traits;

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
        string       $issuerIdentifier,
        string       $subjectIdentifier,
        string|array $audiences,
        int          $expirationTime,
        int          $issuedTime
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
        $header  = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payloadValues);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, 'fakeKey', true);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
}
