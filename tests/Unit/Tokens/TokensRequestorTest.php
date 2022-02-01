<?php

namespace Unit\Tokens;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\Support\Traits\GenerateUserIdJwt;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\UserIdTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\Models\Tokens;
use Pinnacle\OpenIdConnect\Tokens\TokensRequestor;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TokensRequestorTest extends TestCase
{
    use GenerateUserIdJwt;

    /**
     * @test
     */
    public function fetchTokensForAuthorizationCode_ClientThrowsGuzzleException_ThrowsExpectedException(): void
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

        $redirectUri = new Uri('https//endpoint.test/redirect');

        $challenge = Challenge::createWithRandomString();

        Mockery::mock('overload:' . Client::class)
               ->shouldReceive('request')
               ->andThrow(
                   new RequestException('Request Exception', new Request('GET', 'test'))
               );

        $tokensRequestor = new TokensRequestor($provider, $redirectUri, $challenge);

        // Assert
        $this->expectException(OpenIdConnectException::class);

        // Act
        $tokensRequestor->fetchTokensForAuthorizationCode(new AuthorizationCode('authorization-code'));
    }

    /**
     * @test
     */
    public function fetchTokensForAuthorizationCode_UtilsThrowsInvalidArgumentException_ThrowsExpectedException(): void
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

        $redirectUri = new Uri('https//endpoint.test/redirect');

        $challenge = Challenge::createWithRandomString();

        $streamInterface = Mockery::spy(StreamInterface::class);
        $streamInterface->shouldReceive('getContents')
                        ->andReturn('contents');

        $requestMock = Mockery::spy(RequestInterface::class);
        $requestMock->shouldReceive('getBody')
                    ->andReturn($streamInterface);

        $clientMock = Mockery::spy('overload:' . Client::class);
        $clientMock->shouldReceive('request')
                   ->andReturn($requestMock);

        $utilsMock = Mockery::mock('overload:' . Utils::class);
        $utilsMock->shouldReceive('jsonDecode')
                  ->andThrow(InvalidArgumentException::class);

        $tokensRequestor = new TokensRequestor($provider, $redirectUri, $challenge);

        // Assert
        $this->expectException(OpenIdConnectException::class);

        // Act
        $tokensRequestor->fetchTokensForAuthorizationCode(new AuthorizationCode('authorization-code'));
    }

    /**
     * @test
     */
    public function fetchTokensForAuthorizationCode_ResponseMissingAccessToken_ThrowsExpectedException(): void
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

        $redirectUri = new Uri('https//endpoint.test/redirect');

        $challenge = Challenge::createWithRandomString();

        $streamInterface = Mockery::spy(StreamInterface::class);
        $streamInterface->shouldReceive('getContents')
                        ->andReturn(
                            json_encode(
                                [
                                    'id_token' => 'fake-id-token',
                                ]
                            )
                        );

        $requestMock = Mockery::spy(RequestInterface::class);
        $requestMock->shouldReceive('getBody')
                    ->andReturn($streamInterface);

        $clientMock = Mockery::spy('overload:' . Client::class);
        $clientMock->shouldReceive('request')
                   ->andReturn($requestMock);

        $tokensRequestor = new TokensRequestor($provider, $redirectUri, $challenge);

        // Assert
        $this->expectException(AccessTokenNotFoundException::class);

        // Act
        $tokensRequestor->fetchTokensForAuthorizationCode(new AuthorizationCode('authorization-code'));
    }

    /**
     * @test
     */
    public function fetchTokensForAuthorizationCode_ResponseMissingIdToken_ThrowsExpectedException(): void
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

        $redirectUri = new Uri('https//endpoint.test/redirect');

        $challenge = Challenge::createWithRandomString();

        $streamInterface = Mockery::spy(StreamInterface::class);
        $streamInterface->shouldReceive('getContents')
                        ->andReturn(
                            json_encode(
                                [
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

        $tokensRequestor = new TokensRequestor($provider, $redirectUri, $challenge);

        // Assert
        $this->expectException(UserIdTokenNotFoundException::class);

        // Act
        $tokensRequestor->fetchTokensForAuthorizationCode(new AuthorizationCode('authorization-code'));
    }

    /**
     * @test
     */
    public function fetchTokensForAuthorizationCodeSuccessFullRequest_ReturnsTokens(): void
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

        $redirectUri = new Uri('https//endpoint.test/redirect');

        $challenge = Challenge::createWithRandomString();

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

        $tokensRequestor = new TokensRequestor($provider, $redirectUri, $challenge);

        // Act
        $tokens = $tokensRequestor->fetchTokensForAuthorizationCode(new AuthorizationCode('authorization-code'));

        // Assert
        $this->assertInstanceOf(Tokens::class, $tokens);
    }
}
