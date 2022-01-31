<?php

namespace Unit\Authorization;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authorization\TokensResponse;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;
use Pinnacle\OpenIdConnect\Tokens\Models\AccessToken;

class TokensResponseTest extends TestCase
{
    /**
     * @test
     */
    public function getAccessToken_ReturnsExpectedAccessToken(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $accessToken           = new AccessToken('access-token');
        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $tokensResponse = new TokensResponse($accessToken, $providerConfiguration);

        // Act
        $returnedAccessToken = $tokensResponse->getAccessToken();

        // Assert
        $this->assertSame($accessToken, $returnedAccessToken);
    }

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

        $accessToken           = new AccessToken('access-token');
        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $tokensResponse = new TokensResponse($accessToken, $providerConfiguration);

        // Act
        $returnedProvider = $tokensResponse->getProvider();

        // Assert
        $this->assertSame($providerConfiguration, $returnedProvider);
    }
}
