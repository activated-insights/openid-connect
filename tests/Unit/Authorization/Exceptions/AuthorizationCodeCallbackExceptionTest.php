<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Authorization\Exceptions;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authorization\Constants\AuthenticationErrorCode;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\AuthorizationCodeCallbackException;

class AuthorizationCodeCallbackExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function construct_WithErrorDescription_HasExpectedMessage(): void
    {
        // Assemble
        $description = 'Error description test.';

        // Act
        $exception = new AuthorizationCodeCallbackException(
            AuthenticationErrorCode::REGISTRATION_NOT_SUPPORTED(),
            $description
        );

        // Assert
        $this->assertEquals($description, $exception->getMessage());
    }

    /**
     * @test
     */
    public function construct_WithoutErrorDescription_HasExpectedMessage(): void
    {
        // Act
        $exception = new AuthorizationCodeCallbackException(
            AuthenticationErrorCode::REGISTRATION_NOT_SUPPORTED(),
            null
        );

        // Assert
        $this->assertEquals(
            AuthenticationErrorCode::getDescription(AuthenticationErrorCode::REGISTRATION_NOT_SUPPORTED()->getValue()),
            $exception->getMessage()
        );
    }
}
