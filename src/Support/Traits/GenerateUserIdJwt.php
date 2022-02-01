<?php

namespace Pinnacle\OpenIdConnect\Support\Traits;

trait GenerateUserIdJwt
{
    private function generateRandomJwt(): string
    {
        return $this->generateJwtWithRequiredValues(
            'https://test.dev',
            'subject-identifier',
            'audience',
            1311281970,
            1311280970
        );
    }

    private function generateJwtWithRequiredValues(
        string       $issuerIdentifier,
        string       $subjectIdentifier,
        string|array $audiences,
        int          $expirationTime,
        int          $issuedTime
    ): string {
        return $this->generateFakeJwtWithPayloadValues(
            [
                'iss' => $issuerIdentifier,
                'sub' => $subjectIdentifier,
                'aud' => $audiences,
                'exp' => $expirationTime,
                'iat' => $issuedTime,
            ]
        );
    }

    private function generateFakeJwtWithPayloadValues(array $payloadValues): string
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
