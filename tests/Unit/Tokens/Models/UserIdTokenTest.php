<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Tokens\Models;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Tests\Traits\GenerateUserIdJwt;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\InvalidUserIdTokenException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\MissingRequiredClaimKeyException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\UserIdTokenHasExpiredException;
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
    public function construct_MissingIssuer_ThrowsException(): void
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
    public function construct_MissingSubjectIdentifier_ThrowsException(): void
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
    public function construct_MissingAudience_ThrowsException(): void
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
    public function construct_MissingExpirationTime_ThrowsException(): void
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
    public function construct_MissingIssuedTime_ThrowsException(): void
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
    public function construct_WithExpiredToken_ThrowsExpectedException(): void
    {
        // Assemble
        $currentDateTime = new DateTimeImmutable();

        $issuerIdentifier  = 'https://example.com';
        $subjectIdentifier = '1203212312';
        $audience          = 'audience';
        $expirationTime    = (new DateTimeImmutable())->setTimestamp($currentDateTime->getTimestamp() - 60);
        $issuedTime        = (new DateTimeImmutable())->setTimestamp($currentDateTime->getTimestamp());

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $issuerIdentifier,
                'sub' => $subjectIdentifier,
                'aud' => $audience,
                'exp' => $expirationTime->getTimestamp(),
                'iat' => $issuedTime->getTimestamp(),
            ]
        );

        // Assert
        $this->expectException(UserIdTokenHasExpiredException::class);

        // Act
        new UserIdToken($token);
    }

    /**
     * @test
     */
    public function construct_ValidTokens_ReturnsExpectedValues(): void
    {
        // Assert
        $currentDateTime = new DateTimeImmutable();

        $expectedIssuerIdentifier  = 'https://example.com';
        $expectedSubjectIdentifier = '1203212312';
        $expectedAudience          = 'sdlakjfaldj';
        $expectedExpirationTime    = (new DateTimeImmutable())->setTimestamp($currentDateTime->getTimestamp() + 60);
        $expectedIssuedTime        = (new DateTimeImmutable())->setTimestamp($currentDateTime->getTimestamp());

        $token = $this->generateJwtWithPayloadValues(
            [
                'iss' => $expectedIssuerIdentifier,
                'sub' => $expectedSubjectIdentifier,
                'aud' => $expectedAudience,
                'exp' => $expectedExpirationTime->getTimestamp(),
                'iat' => $expectedIssuedTime->getTimestamp(),
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
    public function construct_WithMultipleAudiences_ReturnsExpectedValues(): void
    {
        // Assemble
        $issuerIdentifier  = 'https://example.com';
        $subjectIdentifier = '1203212312';
        $audiences         = ['audience-one', 'audience-two'];
        $expirationTime    = time() + 60;
        $issuedTime        = time();

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
