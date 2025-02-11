<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Mockery;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\AuthenticationUriBuilder;
use Pinnacle\OpenIdConnect\Authentication\Constants\StateKey;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Authenticator;
use Pinnacle\OpenIdConnect\Authorization\AuthorizationCodeResponse;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Authorization\TokensResponse;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;
use Pinnacle\OpenIdConnect\Support\Exceptions\InsecureUriException;
use Pinnacle\OpenIdConnect\Tests\Traits\GenerateUserIdJwt;
use Pinnacle\OpenIdConnect\Tests\Unit\Logger\TestLogger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LogLevel;

class AuthenticatorTest extends TestCase
{
    use GenerateUserIdJwt;

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

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
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

        $provider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
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

        $expectedProvider = new ProviderConfiguration(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint
        );

        $challenge = Challenge::createWithRandomString();
        $state     = State::createWithRandomString();

        $expectedCalls = [
            [StateKey::CHALLENGE()->withPrefix($state->getValue()), $challenge->getValue()],
            [StateKey::PROVIDER_IDENTIFIER()->withPrefix($state->getValue()), $identifier->getValue()],
            [StateKey::PROVIDER_CLIENT_ID()->withPrefix($state->getValue()), $clientId->getValue()],
            [StateKey::PROVIDER_CLIENT_SECRET()->withPrefix($state->getValue()), $clientSecret->getValue()],
            [StateKey::PROVIDER_AUTHORIZATION_ENDPOINT()->withPrefix($state->getValue()), (string)$authorizationEndpoint],
            [StateKey::PROVIDER_TOKEN_ENDPOINT()->withPrefix($state->getValue()), (string)$tokenEndpoint],
            [StateKey::REDIRECT_URI()->withPrefix($state->getValue()), (string)$secureRedirectUri],
        ];

        $statePersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();

        $statePersister->expects($this->exactly(count($expectedCalls)))
            ->method('getValue')
            ->willReturnCallback(function ($key) use (&$expectedCalls) {
                $expectedCall = array_shift($expectedCalls);

                // Verify the key
                $this->assertSame($expectedCall[0], $key);

                // Return the expected value
                return $expectedCall[1];
            });

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
        // Assemble
        $authorizationCode     = new AuthorizationCode('fake-authorization-code');
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

        $redirectUri = new Uri('https//endpoint.test/redirect');

        $challenge = Challenge::createWithRandomString();

        $authorizationCodeResponse = new AuthorizationCodeResponse(
            $authorizationCode,
            $provider,
            $redirectUri,
            $challenge
        );

        $streamInterface = Mockery::spy(StreamInterface::class);
        $streamInterface->shouldReceive('getContents')
                        ->andReturn(
                            json_encode(
                                [
                                    'id_token'     => $this->generateRandomJwt(),
                                    'access_token' => 'fake-access-token',
                                ]
                            )
                        );

        $requestMock = Mockery::spy(RequestInterface::class);
        $requestMock->shouldReceive('getBody')
                    ->andReturn($streamInterface);

        $clientMock = Mockery::spy('overload:' . Client::class);
        $clientMock->shouldReceive('request')
                   ->andReturn($requestMock);

        $statePersister = $this->getMockBuilder(StatePersisterInterface::class)->getMock();

        $logger        = new TestLogger();
        $authenticator = new Authenticator($statePersister, $logger);

        // Act
        $tokensResponse = $authenticator->fetchTokensWithAuthorizationCode($authorizationCodeResponse);

        // Assert
        $this->assertInstanceOf(TokensResponse::class, $tokensResponse);
        $this->assertSame(LogLevel::DEBUG, $logger->latestLevel);
        $this->assertStringContainsString('OIDC: Received OAuth TOKENS endpoint response:', $logger->latestMessage);
    }
}
