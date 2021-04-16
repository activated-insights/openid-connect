<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Pinnacle\OpenidConnect\Dtos\ProviderDto;
use Pinnacle\OpenidConnect\Dtos\UserInfoDto;
use Pinnacle\OpenidConnect\Exceptions\OAuthFailedException;
use stdClass;

class RequestUserInfo
{
    /**
     * @throws OAuthFailedException
     */
    public static function execute(ProviderDto $provider, string $accessToken): UserInfoDto
    {
        $jsonResponse = self::requestUserInfo($provider, $accessToken);

        return UserInfoDto::createWithJson($jsonResponse);
    }

    /**
     * @throws OAuthFailedException
     */
    private static function requestUserInfo(ProviderDto $provider, string $accessToken): stdClass
    {
        try {
            $client = new Client();

            $request = $client
                ->request(
                    'GET',
                    $provider->getUserInfoEndpoint(),
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer ' . $accessToken,
                        ],
                    ]
                );
        } catch (GuzzleException $exception) {
            throw new OAuthFailedException('Unable to retrieve UserInfo from USERINFO endpoint.', 0, $exception);
        }

        try {
            $jsonObject = Utils::jsonDecode($request->getBody()->getContents());
            assert($jsonObject instanceof stdClass);

            return $jsonObject;
        } catch (InvalidArgumentException $exception) {
            throw new OAuthFailedException('Unable to parse JSON response from USERINFO endpoint.', 0, $exception);
        }
    }
}
