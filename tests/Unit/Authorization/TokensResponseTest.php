<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Authorization;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authorization\TokensResponse;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;
use Pinnacle\OpenIdConnect\Tests\Traits\GenerateUserIdJwt;
use Pinnacle\OpenIdConnect\Tokens\Models\AccessToken;
use Pinnacle\OpenIdConnect\Tokens\Models\RefreshToken;
use Pinnacle\OpenIdConnect\Tokens\Models\Tokens;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\UserIdToken;

class TokensResponseTest extends TestCase
{
    use GenerateUserIdJwt;

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

        $accessToken  = new AccessToken('access-token');
        $refreshToken = new RefreshToken('refresh-token');
        $userIdToken  = new UserIdToken($this->generateRandomJwt());

        $tokens = new Tokens($accessToken, $refreshToken, $userIdToken);

        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $tokensResponse = new TokensResponse($tokens, $providerConfiguration);

        // Act
        $returnedAccessToken = $tokensResponse->getAccessToken();

        // Assert
        $this->assertSame($accessToken, $returnedAccessToken);
    }

    /**
     * @test
     */
    public function getRefreshToken_ReturnsExpectedAccessToken(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $accessToken  = new AccessToken('access-token');
        $refreshToken = new RefreshToken('refresh-token');
        $userIdToken  = new UserIdToken($this->generateRandomJwt());

        $tokens = new Tokens($accessToken, $refreshToken, $userIdToken);

        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $tokensResponse = new TokensResponse($tokens, $providerConfiguration);

        // Act
        $returnedRefreshToken  = $tokensResponse->getRefreshToken();

        // Assert
        $this->assertSame($refreshToken, $returnedRefreshToken);
    }

    /**
     * @test
     */
    public function getUserIdToken_ReturnsExpectedUserIdToken(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $accessToken  = new AccessToken('access-token');
        $refreshToken = new RefreshToken('refresh-token');
        $userIdToken  = new UserIdToken($this->generateRandomJwt());

        $tokens = new Tokens($accessToken, $refreshToken, $userIdToken);

        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $tokensResponse = new TokensResponse($tokens, $providerConfiguration);

        // Act
        $returnedUserIdToken = $tokensResponse->getUserIdToken();

        // Assert
        $this->assertSame($userIdToken, $returnedUserIdToken);
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

        $accessToken  = new AccessToken('access-token');
        $refreshToken = new RefreshToken('refresh-token');
        $userIdToken  = new UserIdToken($this->generateRandomJwt());

        $tokens = new Tokens($accessToken, $refreshToken, $userIdToken);

        $providerConfiguration = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $tokensResponse = new TokensResponse($tokens, $providerConfiguration);

        // Act
        $returnedProvider = $tokensResponse->getProvider();

        // Assert
        $this->assertSame($providerConfiguration, $returnedProvider);
    }
}
