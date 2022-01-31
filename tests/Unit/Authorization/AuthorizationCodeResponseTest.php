<?php

namespace Unit\Authorization;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authorization\AuthorizationCodeResponse;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;

class AuthorizationCodeResponseTest extends TestCase
{
    /**
     * @test
     */
    public function getProvider_ReturnsExpectedProvider(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $authorizationCode     = new AuthorizationCode('authorization-code');
        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );
        $redirectUri           = new Uri('https://uri.test/redirect');
        $challenge             = Challenge::createWithRandomString();

        $authorizationCodeResponse = new AuthorizationCodeResponse(
            $authorizationCode,
            $providerConfiguration,
            $redirectUri,
            $challenge
        );

        // Act
        $returnedProvider = $authorizationCodeResponse->getProvider();

        // Assert
        $this->assertSame($providerConfiguration, $returnedProvider);
    }

    public function getAuthorizationCode_ReturnsExpectedAuthorizationCode(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $authorizationCode     = new AuthorizationCode('authorization-code');
        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );
        $redirectUri           = new Uri('https://uri.test/redirect');
        $challenge             = Challenge::createWithRandomString();

        $authorizationCodeResponse = new AuthorizationCodeResponse(
            $authorizationCode,
            $providerConfiguration,
            $redirectUri,
            $challenge
        );

        // Act
        $returnedAuthorizationCode = $authorizationCodeResponse->getAuthorizationCode();

        // Assert
        $this->assertSame($providerConfiguration, $returnedAuthorizationCode);
    }

    public function getRedirectUri_ReturnsExpectedRedirectUri(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $authorizationCode     = new AuthorizationCode('authorization-code');
        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );
        $redirectUri           = new Uri('https://uri.test/redirect');
        $challenge             = Challenge::createWithRandomString();

        $authorizationCodeResponse = new AuthorizationCodeResponse(
            $authorizationCode,
            $providerConfiguration,
            $redirectUri,
            $challenge
        );

        // Act
        $returnedRedirectUri = $authorizationCodeResponse->getRedirectUri();

        // Assert
        $this->assertSame($providerConfiguration, $returnedRedirectUri);
    }

    public function getChallenge_ReturnsExpectedChallenge(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $authorizationCode     = new AuthorizationCode('authorization-code');
        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );
        $redirectUri           = new Uri('https://uri.test/redirect');
        $challenge             = Challenge::createWithRandomString();

        $authorizationCodeResponse = new AuthorizationCodeResponse(
            $authorizationCode,
            $providerConfiguration,
            $redirectUri,
            $challenge
        );

        // Act
        $returnedChallenge = $authorizationCodeResponse->getChallenge();

        // Assert
        $this->assertSame($providerConfiguration, $returnedChallenge);
    }
}
