<?php

namespace Unit\Tokens\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Support\Traits\GenerateUserIdJwt;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\InvalidUserIdTokenException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\MissingRequiredClaimKeyException;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\Audience;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\UserIdToken;

class UserIdTokenTest extends TestCase
{
    use GenerateUserIdJwt;

    /**
     * @test
     */
    public function construct_WithEmptyString_ThrowsException(): void
    {
        // Assemble
        $invalidToken = '';

        // Assert
        $this->expectException(InvalidUserIdTokenException::class);

        // Act
        new UserIdToken($invalidToken);
    }

    /**
     * @test
     */
    public function construct_WithInvalidSectionCount_ThrowsException(): void
    {
        // Assemble
        $invalidToken = 'section1.section2';

        // Assert
        $this->expectException(InvalidUserIdTokenException::class);

        // Act
        new UserIdToken($invalidToken);
    }

    /**
     * @test
     */
    public function construct_WithInvalidTokenPayload_ThrowsException(): void
    {
        // Assemble
        $invalidToken = 'header.invalid-payload.signature';

        // Assert
        $this->expectException(InvalidUserIdTokenException::class);

        // Act
        new UserIdToken($invalidToken);
    }

    /**
     * @test
     */
    public function construct_missingIssuer_ThrowsException(): void
    {
        // Assemble
        $expectedSubjectIdentifier = '1203212312';
        $expectedAudience          = 'sdlakjfaldj';
        $expectedExpirationTime    = 1311281970;
        $expectedIssuedTime        = 1311280970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'sub' => $expectedSubjectIdentifier,
                'aud' => $expectedAudience,
                'exp' => $expectedExpirationTime,
                'iat' => $expectedIssuedTime,
            ]
        );

        // Assert
        $this->expectException(MissingRequiredClaimKeyException::class);

        // Act
        new UserIdToken($token);
    }

    /**
     * @test
     */
    public function construct_missingSubjectIdentifier_ThrowsException(): void
    {
        // Assemble
        $expectedIssuerIdentifier = 'https://example.com';
        $expectedAudience         = 'sdlakjfaldj';
        $expectedExpirationTime   = 1311281970;
        $expectedIssuedTime       = 1311280970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $expectedIssuerIdentifier,
                'aud' => $expectedAudience,
                'exp' => $expectedExpirationTime,
                'iat' => $expectedIssuedTime,
            ]
        );

        // Assert
        $this->expectException(MissingRequiredClaimKeyException::class);

        // Act
        new UserIdToken($token);
    }

    /**
     * @test
     */
    public function construct_missingAudience_ThrowsException(): void
    {
        // Assemble
        $expectedIssuerIdentifier  = 'https://example.com';
        $expectedSubjectIdentifier = '1203212312';
        $expectedExpirationTime    = 1311281970;
        $expectedIssuedTime        = 1311280970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $expectedIssuerIdentifier,
                'sub' => $expectedSubjectIdentifier,
                'exp' => $expectedExpirationTime,
                'iat' => $expectedIssuedTime,
            ]
        );

        // Assert
        $this->expectException(MissingRequiredClaimKeyException::class);

        // Act
        new UserIdToken($token);
    }

    /**
     * @test
     */
    public function construct_missingExpirationTime_ThrowsException(): void
    {
        // Assemble
        $expectedIssuerIdentifier  = 'https://example.com';
        $expectedSubjectIdentifier = '1203212312';
        $expectedAudience          = 'sdlakjfaldj';
        $expectedIssuedTime        = 1311280970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $expectedIssuerIdentifier,
                'sub' => $expectedSubjectIdentifier,
                'aud' => $expectedAudience,
                'iat' => $expectedIssuedTime,
            ]
        );

        // Assert
        $this->expectException(MissingRequiredClaimKeyException::class);

        // Act
        new UserIdToken($token);
    }

    /**
     * @test
     */
    public function construct_missingIssuedTime_ThrowsException(): void
    {
        // Assemble
        $expectedIssuerIdentifier  = 'https://example.com';
        $expectedSubjectIdentifier = '1203212312';
        $expectedAudience          = 'sdlakjfaldj';
        $expectedExpirationTime    = 1311281970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $expectedIssuerIdentifier,
                'sub' => $expectedSubjectIdentifier,
                'aud' => $expectedAudience,
                'exp' => $expectedExpirationTime,
            ]
        );

        // Assert
        $this->expectException(MissingRequiredClaimKeyException::class);

        // Act
        new UserIdToken($token);
    }

    /**
     * @test
     */
    public function construct_validTokens_returnsExpectedValues(): void
    {
        // Assert
        $expectedIssuerIdentifier  = 'https://example.com';
        $expectedSubjectIdentifier = '1203212312';
        $expectedAudience          = 'sdlakjfaldj';
        $expectedExpirationTime    = 1311281970;
        $expectedIssuedTime        = 1311280970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $expectedIssuerIdentifier,
                'sub' => $expectedSubjectIdentifier,
                'aud' => $expectedAudience,
                'exp' => $expectedExpirationTime,
                'iat' => $expectedIssuedTime,
            ]
        );

        // Act
        $userIdToken = new UserIdToken($token);

        // Assemble
        $this->assertEquals($expectedIssuerIdentifier, $userIdToken->getIssuerIdentifier());
        $this->assertEquals($expectedSubjectIdentifier, $userIdToken->getSubjectIdentifier()->getValue());
        $this->assertEquals($expectedAudience, $userIdToken->getAudiences()->getAudiences()[0]->getValue());
        $this->assertEquals($expectedExpirationTime, $userIdToken->getExpirationTime());
        $this->assertEquals($expectedIssuedTime, $userIdToken->getIssuedTime());
    }

    /**
     * @test
     */
    public function construct_withMultipleAudiences_returnsExpectedValues(): void
    {
        // Assemble
        $issuerIdentifier  = 'https://example.com';
        $subjectIdentifier = '1203212312';
        $audiences         = ['audience-one', 'audience-two'];
        $expirationTime    = 1311281970;
        $issuedTime        = 1311280970;

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $issuerIdentifier,
                'sub' => $subjectIdentifier,
                'aud' => $audiences,
                'exp' => $expirationTime,
                'iat' => $issuedTime,
            ]
        );

        // Act
        $userIdToken = new UserIdToken($token);

        // Assert
        $expectedAudiences = [];
        foreach ($audiences as $audience) {
            $expectedAudiences[] = new Audience($audience);
        }

        $this->assertEquals($expectedAudiences, $userIdToken->getAudiences()->getAudiences());
    }
}
