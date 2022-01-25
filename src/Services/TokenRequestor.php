<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Pinnacle\OpenIdConnect\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Models\Contracts\ProviderInterface;
use Pinnacle\OpenIdConnect\Exceptions\OpenIdRequestFailedException;
use Psr\Log\LoggerInterface;
use stdClass;

class RequestTokens
{
    public function __construct(
        private ProviderInterface $provider,
        private Uri               $redirectUri,
        private string            $codeVerifier,
        private ?LoggerInterface  $logger = null
    ) {
    }

    /**
     * @throws OpenIdRequestException
     * @throws AccessTokenNotFoundException
     */
    public function getAccessTokenForAuthorizationCode(string $authorizationCode): string
    {
        $response = $this->requestTokens($authorizationCode);

        return $this->accessTokenFromJsonResponse($response);
    }

    /**
     * @throws OpenIdRequestException
     */
    private function requestTokens(string $authorizationCode): stdClass
    {
        try {
            $client = new Client();

            $formParams = [
                'grant_type'    => 'authorization_code',
                'client_id'     => $this->provider->getClientId(),
                'redirect_uri'  => (string)$this->redirectUri,
                'code'          => $authorizationCode,
                'code_verifier' => $this->codeVerifier,
            ];

            $this->logger?->debug(
                sprintf(
                    'OIDC: Sending POST to %s with parameters %s and client ID %s.',
                    $this->provider->getTokenEndpoint(),
                    Utils::jsonEncode($formParams),
                    $this->provider->getClientId()
                )
            );

            $request = $client
                ->request(
                    'POST',
                    $this->provider->getTokenEndpoint(),
                    [
                        // Authenticate with TOKEN endpoint using client ID and secret
                        RequestOptions::AUTH        => [
                            $this->provider->getClientId(),
                            $this->provider->getClientSecret(),
                        ],
                        RequestOptions::FORM_PARAMS => $formParams,
                        RequestOptions::TIMEOUT     => 15, // in seconds
                    ]
                );
        } catch (GuzzleException $exception) {
            throw new OpenIdRequestFailedException('Unable to retrieve OAuth tokens from IdP endpoint.', 0, $exception);
        }

        try {
            $response = $request->getBody()->getContents();

            $this->logger?->debug(sprintf('OIDC: Received OAuth TOKENS endpoint response: %s.', $response));

            $jsonObject = Utils::jsonDecode($response);
            assert($jsonObject instanceof stdClass);

            return $jsonObject;
        } catch (InvalidArgumentException $exception) {
            throw new OpenIdRequestFailedException('Unable to parse JSON response from TOKENS endpoint.', 0, $exception);
        }
    }

    /**
     * @throws AccessTokenNotFoundException
     */
    private static function accessTokenFromJsonResponse(stdClass $jsonResponse): string
    {
        if (!isset($jsonResponse->access_token)) {
            throw new AccessTokenNotFoundException(
                sprintf('access_token not found in JSON response %s.', json_encode($jsonResponse))
            );
        }

        return (string)$jsonResponse->access_token;
    }
}
