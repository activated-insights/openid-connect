<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Tokens;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\UserIdTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\Models\AccessToken;
use Pinnacle\OpenIdConnect\Tokens\Models\Tokens;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\UserIdToken;
use Psr\Log\LoggerInterface;
use stdClass;

class TokensRequestor
{
    private const GRANT_TYPE = 'authorization_code';

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
     * @throws UserIdTokenNotFoundException
     */
    public function fetchTokensForAuthorizationCode(AuthorizationCode $authorizationCode): Tokens
    {
        try {
            $client = new Client();

            $formParams = [
                'grant_type'    => self::GRANT_TYPE,
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

            return $this->tokensFromJsonResponse($jsonObject);
        } catch (InvalidArgumentException $exception) {
            throw new OpenIdConnectException('Unable to parse JSON response from TOKENS endpoint.', 0, $exception);
        }
    }

    /**
     * @throws AccessTokenNotFoundException
     */
    private static function tokensFromJsonResponse(stdClass $jsonResponse): Tokens
    {
        if (!isset($jsonResponse->access_token)) {
            throw new AccessTokenNotFoundException(
                sprintf('access_token not found in JSON response %s.', json_encode($jsonResponse))
            );
        }

        $accessToken = new AccessToken($jsonResponse->access_token);

        if (!isset($jsonResponse->id_token)) {
            throw new UserIdTokenNotFoundException(
                sprintf('id_token not found in JSON response %s.', json_encode($jsonResponse))
            );
        }

        $userIdToken = new UserIdToken($jsonResponse->id_token);

        return new Tokens($accessToken, $userIdToken);
    }
}
