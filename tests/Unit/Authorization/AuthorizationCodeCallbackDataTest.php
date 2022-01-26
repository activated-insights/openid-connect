<?php

namespace Unit\Authorization;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authorization\AuthorizationCodeCallbackData;
use Pinnacle\OpenIdConnect\Authorization\Constants\AuthenticationErrorCode;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\AuthorizationCodeCallbackException;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\MissingRequiredQueryParametersException;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;

class AuthorizationCodeCallbackDataTest extends TestCase
{
    /**
     * @test
     */
    public function construct_WithErrorCode_ThrowsExpectedException(): void
    {
        // Assemble
        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'state'             => State::createWithRandomString()->getValue(),
                    'error'             => AuthenticationErrorCode::REGISTRATION_NOT_SUPPORTED()->getValue(),
                    'error_description' => 'Error Description Test.',
                ]
            )
        );

        // Assert
        $this->expectException(AuthorizationCodeCallbackException::class);

        // Act
        new AuthorizationCodeCallbackData($callbackUri);
    }

    /**
     * @test
     */
    public function construct_MissingAuthorizationCode_ThrowsExpectedException(): void
    {
        // Assemble
        $state = State::createWithRandomString();

        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'state' => $state->getValue(),
                ]
            )
        );

        // Assert
        $this->expectException(MissingRequiredQueryParametersException::class);

        // Act
        new AuthorizationCodeCallbackData($callbackUri);
    }

    /**
     * @test
     */
    public function construct_MissingState_ThrowsExpectedException(): void
    {
        // Assemble
        $authorizationCode = new AuthorizationCode('authorization-code');

        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'code' => $authorizationCode,
                ]
            )
        );

        // Assert
        $this->expectException(MissingRequiredQueryParametersException::class);

        // Act
        new AuthorizationCodeCallbackData($callbackUri);
    }

    /**
     * @test
     */
    public function construct_MissingChallenge_ThrowsExpectedException(): void
    {
        // Assemble
        $authorizationCode = new AuthorizationCode('authorization-code');
        $state             = State::createWithRandomString();

        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'code'  => $authorizationCode->getValue(),
                    'state' => $state,
                ]
            )
        );

        // Assert
        $this->expectException(MissingRequiredQueryParametersException::class);

        // Act
        new AuthorizationCodeCallbackData($callbackUri);
    }

    /**
     * @test
     */
    public function getAuthorizationCode_WithAuthorizationCode_ReturnsAuthorizationCode(): void
    {
        // Assemble
        $authorizationCode = new AuthorizationCode('authorization-code');
        $state             = State::createWithRandomString();

        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'code'  => $authorizationCode->getValue(),
                    'state' => $state->getValue(),
                ]
            )
        );

        // Act
        $callbackData = new AuthorizationCodeCallbackData($callbackUri);

        // Assert
        $this->assertInstanceOf(AuthorizationCode::class, $callbackData->getAuthorizationCode());
    }

    /**
     * @test
     */
    public function getState_WithState_ReturnsState(): void
    {
        // Assemble
        $authorizationCode = new AuthorizationCode('authorization-code');
        $state             = State::createWithRandomString();

        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'code'  => $authorizationCode->getValue(),
                    'state' => $state->getValue(),
                ]
            )
        );

        // Act
        $callbackData = new AuthorizationCodeCallbackData($callbackUri);

        // Assert
        $this->assertInstanceOf(State::class, $callbackData->getState());
    }
}
