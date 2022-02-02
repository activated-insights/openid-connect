<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Authentication;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\AuthenticationUriBuilder;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;

class AuthenticationUriBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function getState_ReturnsStateObject(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $redirectUri = new Uri('https://uri.test/redirect');

        $authenticationUriBuilder = new AuthenticationUriBuilder($provider, $redirectUri);

        // Act
        $state = $authenticationUriBuilder->getState();

        // Assert
        $this->assertInstanceOf(State::class, $state);
    }

    /**
     * @test
     */
    public function getCodeChallenge_ReturnsChallengeObject(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $redirectUri = new Uri('https://uri.test/redirect');

        $authenticationUriBuilder = new AuthenticationUriBuilder($provider, $redirectUri);

        // Act
        $challenge = $authenticationUriBuilder->getCodeChallenge();

        // Assert
        $this->assertInstanceOf(Challenge::class, $challenge);
    }

    /**
     * @test
     */
    public function uri_WithDefaultScopes_ReturnsExpectedUri(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $redirectUri = new Uri('https://uri.test/redirect');

        $authenticationUriBuilder = new AuthenticationUriBuilder($provider, $redirectUri);

        // Act
        $generatedUri = $authenticationUriBuilder->uri();

        $this->assertEquals($authorizationEndpoint->getHost(), $generatedUri->getHost());
        $this->assertEquals($authorizationEndpoint->getAuthority(), $generatedUri->getAuthority());
        $this->assertEquals($authorizationEndpoint->getFragment(), $generatedUri->getFragment());
        $this->assertEquals($authorizationEndpoint->getPath(), $generatedUri->getPath());
        $this->assertEquals($authorizationEndpoint->getScheme(), $generatedUri->getScheme());

        parse_str($generatedUri->getQuery(), $generatedQuery);

        $this->assertEquals('code', $generatedQuery['response_type']);
        $this->assertEquals($clientId->getValue(), $generatedQuery['client_id']);
        $this->assertEquals((string)$redirectUri, $generatedQuery['redirect_uri']);
        $this->assertEquals('openid', $generatedQuery['scope']);
        $this->assertEquals(16, strlen($generatedQuery['state']));
        $this->assertEquals('S256', $generatedQuery['code_challenge_method']);
        $this->assertEquals(43, strlen($generatedQuery['code_challenge']));
    }

    /**
     * @test
     */
    public function uri_WithAdditionalScopes_ReturnsExpectedUri(): void
    {
        // Assemble
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $redirectUri = new Uri('https://uri.test/redirect');

        $authenticationUriBuilder = new AuthenticationUriBuilder($provider, $redirectUri);

        // Act
        $generatedUri = $authenticationUriBuilder->withScopes('foo', 'bar')->uri();

        $this->assertEquals($authorizationEndpoint->getHost(), $generatedUri->getHost());
        $this->assertEquals($authorizationEndpoint->getAuthority(), $generatedUri->getAuthority());
        $this->assertEquals($authorizationEndpoint->getFragment(), $generatedUri->getFragment());
        $this->assertEquals($authorizationEndpoint->getPath(), $generatedUri->getPath());
        $this->assertEquals($authorizationEndpoint->getScheme(), $generatedUri->getScheme());

        parse_str($generatedUri->getQuery(), $generatedQuery);

        $this->assertEquals('code', $generatedQuery['response_type']);
        $this->assertEquals($clientId->getValue(), $generatedQuery['client_id']);
        $this->assertEquals((string)$redirectUri, $generatedQuery['redirect_uri']);
        $this->assertEquals('openid foo bar', $generatedQuery['scope']);
        $this->assertEquals(16, strlen($generatedQuery['state']));
        $this->assertEquals('S256', $generatedQuery['code_challenge_method']);
        $this->assertEquals(43, strlen($generatedQuery['code_challenge']));
    }
}
