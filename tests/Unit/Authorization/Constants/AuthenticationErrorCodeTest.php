<?php

namespace Unit\Authorization\Constants;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authorization\Constants\AuthenticationErrorCode;

class AuthenticationErrorCodeTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validErrorCodeValueDataProvider
     */
    public function getDescription_WithValidErrorCodeValues_DoesNotReturnUnknownErrorMessage(
        string $validErrorCodeValue
    ): void {
        // Act
        $description = AuthenticationErrorCode::getDescription($validErrorCodeValue);

        // Assert
        $this->assertNotEquals(
            sprintf('An unknown error code %s was sent with the authentication request.', $validErrorCodeValue),
            $description
        );
    }

    public function getDescription_WithInvalidErrorCodeValue_DoesReturnUnknownErrorMessage(): void
    {
        // Assemble
        $invalidErrorCodeValue = 'invalid_error_code_test';

        // Act
        $description = AuthenticationErrorCode::getDescription($invalidErrorCodeValue);

        // Assert
        $this->assertEquals(
            sprintf('An unknown error code %s was sent with the authentication request.', $invalidErrorCodeValue),
            $description
        );
    }

    public function validErrorCodeValueDataProvider(): array
    {
        $validErrorCodeValues = [];

        foreach (AuthenticationErrorCode::values() as $authenticationErrorCode) {
            $validErrorCodeValues[] = [
                'validErrorCodeValue' => $authenticationErrorCode->getValue(),
            ];
        }

        return $validErrorCodeValues;
    }
}
