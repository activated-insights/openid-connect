<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Requestors;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Requestors\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Psr\Log\LoggerInterface;
use stdClass;

class TokenRequestor
{
    public function __construct(
        private ProviderConfigurationInterface $provider,
        private Uri                            $redirectUri,
        private Challenge                      $challenge,
        private ?LoggerInterface               $logger = null
    ) {
    }

    /**
     * @throws OpenIdConnectException
     * @throws AccessTokenNotFoundException
     */
    public function fetchTokensForAuthorizationCode(AuthorizationCode $authorizationCode): string
    {
        $response = $this->requestTokens($authorizationCode);

        return $this->accessTokenFromJsonResponse($response);
    }

    /**
     * @throws OpenIdConnectException
     */
    private function requestTokens(AuthorizationCode $authorizationCode): stdClass
    {
        try {
            $client = new Client();

            $formParams = [
                'grant_type'    => 'authorization_code',
                'client_id'     => $this->provider->getClientId()->getValue(),
                'redirect_uri'  => (string)$this->redirectUri,
                'code'          => $authorizationCode->getValue(),
                'code_verifier' => $this->challenge->getValue(),
            ];

            $this->logger?->debug(
                sprintf(
                    'OIDC: Sending POST to %s with parameters %s and client ID %s.',
                    $this->provider->getTokenEndpoint(),
                    Utils::jsonEncode($formParams),
                    $this->provider->getClientId()->getValue()
                )
            );

            $request = $client
                ->request(
                    'POST',
                    $this->provider->getTokenEndpoint(),
                    [
                        // Authenticate with TOKEN endpoint using client ID and secret
                        RequestOptions::AUTH        => [
                            $this->provider->getClientId()->getValue(),
                            $this->provider->getClientSecret()->getValue(),
                        ],
                        RequestOptions::FORM_PARAMS => $formParams,
                        RequestOptions::TIMEOUT     => 15, // in seconds
                    ]
                );
        } catch (GuzzleException $exception) {
            throw new OpenIdConnectException('Unable to retrieve OAuth tokens from IdP endpoint.', 0, $exception);
        }

        try {
            $response = $request->getBody()->getContents();

            $this->logger?->debug(sprintf('OIDC: Received OAuth TOKENS endpoint response: %s.', $response));

            $jsonObject = Utils::jsonDecode($response);
            assert($jsonObject instanceof stdClass);

            return $jsonObject;
        } catch (InvalidArgumentException $exception) {
            throw new OpenIdConnectException('Unable to parse JSON response from TOKENS endpoint.', 0, $exception);
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
