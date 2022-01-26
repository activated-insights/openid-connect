<?php

namespace Unit;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\AuthenticationUriBuilder;
use Pinnacle\OpenIdConnect\Authentication\Constants\StateKey;
use Pinnacle\OpenIdConnect\Authentication\Exceptions\ChallengeMismatchException;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Authenticator;
use Pinnacle\OpenIdConnect\Authorization\AuthorizationCodeResponse;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;
use Pinnacle\OpenIdConnect\Support\Exceptions\InsecureUriException;

class AuthenticatorTest extends TestCase
{
    /**
     * @test
     */
    public function beginAuthentication_WithInsecureRedirectUri_ThrowsExpectedException(): void
    {
        // Assemble
        $statePersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();

        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');
        $userInfoEndpoint      = new Uri('https://endpoint.test/user-info');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint,
            $userInfoEndpoint
        );

        $authenticator = new Authenticator($statePersister);

        $insecureRedirectUri = new Uri('http://uri.test/redirect');

        // Assert
        $this->expectException(InsecureUriException::class);

        // Act
        $authenticator->beginAuthentication($insecureRedirectUri, $provider);
    }

    /**
     * @test
     */
    public function beginAuthentication_WithValidValues_ReturnsAuthenticationUriBuilderObject(): void
    {
        // Assemble
        $statePersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();

        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');
        $userInfoEndpoint      = new Uri('https://endpoint.test/user-info');

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint,
            $userInfoEndpoint
        );

        $authenticator = new Authenticator($statePersister);

        $secureRedirectUri = new Uri('https://uri.test/redirect');

        // Act
        $authenticationUriBuilder = $authenticator->beginAuthentication($secureRedirectUri, $provider);

        // Assert
        $this->assertInstanceOf(AuthenticationUriBuilder::class, $authenticationUriBuilder);
    }

    /**
     * @test
     */
    public function handleAuthorizationCodeCallback_WithValidValues_ReturnsAuthorizationCodeResponseObject(): void
    {
        // Assemble
        $secureRedirectUri     = new Uri('https://uri.test/redirect');
        $identifier            = new Identifier('identifier');
        $clientId              = new ClientId('client-id');
        $clientSecret          = new ClientSecret('client-secret');
        $authorizationEndpoint = new Uri('https://endpoint.test/authorization');
        $tokenEndpoint         = new Uri('https://endpoint.test/token');
        $userInfoEndpoint      = new Uri('https://endpoint.test/user-info');

        $expectedProvider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint,
            $userInfoEndpoint
        );

        $challenge = Challenge::createWithRandomString();
        $state     = State::createWithRandomString();

        $statePersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();

        $statePersister->expects($this->exactly(8))
                       ->method('getValue')
                       ->withConsecutive(
                           [StateKey::CHALLENGE()->withPrefix($state->getValue())],
                           [StateKey::PROVIDER_IDENTIFIER()->withPrefix($state->getValue())],
                           [StateKey::PROVIDER_CLIENT_ID()->withPrefix($state->getValue())],
                           [StateKey::PROVIDER_CLIENT_SECRET()->withPrefix($state->getValue())],
                           [StateKey::PROVIDER_AUTHORIZATION_ENDPOINT()->withPrefix($state->getValue())],
                           [StateKey::PROVIDER_TOKEN_ENDPOINT()->withPrefix($state->getValue())],
                           [StateKey::PROVIDER_USER_INFO_ENDPOINT()->withPrefix($state->getValue())],
                           [StateKey::REDIRECT_URI()->withPrefix($state->getValue())]
                       )
                       ->willReturnOnConsecutiveCalls(
                           $challenge->getValue(),
                           $identifier->getValue(),
                           $clientId->getValue(),
                           $clientSecret->getValue(),
                           (string)$authorizationEndpoint,
                           (string)$tokenEndpoint,
                           (string)$userInfoEndpoint,
                           (string)$secureRedirectUri,
                       );

        $authorizationCode = new AuthorizationCode('authorization-code');

        $callbackUri = new Uri(
            'https://callback.test?' . http_build_query(
                [
                    'code'           => $authorizationCode->getValue(),
                    'state'          => $state->getValue(),
                    'code_challenge' => $challenge->getValue(),
                ]
            )
        );

        $authenticator = new Authenticator($statePersister);

        // Act
        $authorizationCodeResponse = $authenticator->handleAuthorizationCodeCallback($callbackUri);

        // Assert
        $this->assertInstanceOf(AuthorizationCodeResponse::class, $authorizationCodeResponse);
        $this->assertEquals(
            $expectedProvider->getIdentifier(),
            $authorizationCodeResponse->getProvider()->getIdentifier()
        );
        $this->assertEquals($challenge, $authorizationCodeResponse->getChallenge());
        $this->assertEquals((string)$secureRedirectUri, (string)$authorizationCodeResponse->getRedirectUri());
    }

    /**
     * @test
     */
    public function fetchTokensWithAuthorizationCode_WithValidValues_ReturnsTokensResponseObject(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function fetchUserInformationWithAccessToken_WithValidValues_ReturnsUserInfoObject(): void
    {
        $this->markTestIncomplete();
    }
}
