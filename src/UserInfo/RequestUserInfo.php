<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\UserInfo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\Tokens\Models\AccessToken;
use Pinnacle\OpenIdConnect\UserInfo\Models\UserInfo;
use Psr\Log\LoggerInterface;
use stdClass;

class RequestUserInfo
{
    /**
     * @throws OpenIdConnectException
     */
    public static function execute(
        ProviderConfiguration $provider,
        AccessToken           $accessToken,
        ?LoggerInterface      $logger = null
    ): UserInfo {
        $jsonResponse = self::requestUserInfo($provider, $accessToken, $logger);

        return UserInfo::createWithJson($jsonResponse);
    }

    /**
     * @throws OpenIdConnectException
     */
    private static function requestUserInfo(
        ProviderConfiguration $provider,
        AccessToken           $accessToken,
        ?LoggerInterface      $logger = null
    ): stdClass {
        try {
            $client = new Client();

            $logger?->debug(sprintf('OIDC: Sending GET to %s.', $provider->getUserInfoEndpoint()));

            $request = $client
                ->request(
                    'GET',
                    $provider->getUserInfoEndpoint(),
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer ' . $accessToken->getValue(),
                        ],
                        RequestOptions::TIMEOUT => 15, // in seconds
                    ]
                );
        } catch (GuzzleException $exception) {
            throw new OpenIdConnectException('Unable to retrieve UserInfo from USERINFO endpoint.', 0, $exception);
        }

        try {
            $response = $request->getBody()->getContents();

            $logger?->debug(sprintf('OIDC: Received OAuth USERINFO endpoint response: %s.', $response));

            $jsonObject = Utils::jsonDecode($response);
            assert($jsonObject instanceof stdClass);

            return $jsonObject;
        } catch (InvalidArgumentException $exception) {
            throw new OpenIdConnectException('Unable to parse JSON response from USERINFO endpoint.', 0, $exception);
        }
    }
}
