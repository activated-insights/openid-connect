<?php

namespace Pinnacle\OpenIdConnect;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Exceptions\AuthenticationConnectException;
use Pinnacle\OpenIdConnect\Exceptions\InsecureUriProtocolException;
use Pinnacle\OpenIdConnect\Exceptions\MismatchChallengeException;
use Pinnacle\OpenIdConnect\Exceptions\MissingRequiredQueryParametersException;
use Pinnacle\OpenIdConnect\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\Exceptions\StatePersisterMissingValueException;
use Pinnacle\OpenIdConnect\Models\AccessTokenResponse;
use Pinnacle\OpenIdConnect\Models\AuthenticationRequest;
use Pinnacle\OpenIdConnect\Models\AuthenticationUriBuilder;
use Pinnacle\OpenIdConnect\Models\AuthorizationCodeResponse;
use Pinnacle\OpenIdConnect\Models\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Models\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Models\UserInfo;
use Pinnacle\OpenIdConnect\Services\TokenRequestor;
use Pinnacle\OpenIdConnect\Services\RequestUserInfo;
use Pinnacle\OpenIdConnect\Services\StatePersisterWrapper;
use Psr\Log\LoggerInterface;

class Authenticator
{
    public function __construct(
        private StatePersisterInterface $statePersister,
        private ?LoggerInterface        $logger = null
    ) {
    }

    public function beginAuthentication(Uri $redirectUri, ProviderConfigurationInterface $provider,): AuthenticationUriBuilder
    {
        if ($redirectUri->getScheme() !== 'https') {
            throw new InsecureUriProtocolException('Redirect URI must use https');
        }

        $authenticationUriBuilder = new AuthenticationUriBuilder($provider, $redirectUri);

        $state     = $authenticationUriBuilder->getState();
        $challenge = $authenticationUriBuilder->getCodeChallenge();

        $statePersisterWrapper = new StatePersisterWrapper($this->statePersister, $state);

        $statePersisterWrapper->storeChallenge($challenge);
        $statePersisterWrapper->storeProvider($provider);
        $statePersisterWrapper->storeRedirectUri($redirectUri);

        return $authenticationUriBuilder;
    }

    /**
     * @throws MissingRequiredQueryParametersException
     * @throws AuthenticationConnectException
     * @throws MismatchChallengeException
     * @throws StatePersisterMissingValueException
     */
    public function handleAuthorizationCodeCallback(Uri $callbackUri): AuthorizationCodeResponse
    {
        $authenticationRequest = new AuthenticationRequest($callbackUri);

        $responseState = $authenticationRequest->getState();

        $statePersisterWrapper = new StatePersisterWrapper($this->statePersister, $responseState);

        $challenge   = $statePersisterWrapper->getChallenge();
        $provider    = $statePersisterWrapper->getProvider();
        $redirectUri = $statePersisterWrapper->getRedirectUri();

        if ($authenticationRequest->getChallenge() !== $challenge) {
            throw new MismatchChallengeException(
                sprintf(
                    'Response challenge %s does not match the original request %s.',
                    $authenticationRequest->getChallenge(),
                    $challenge
                )
            );
        }

        return new AuthorizationCodeResponse(
            $authenticationRequest->getAuthorizationCode(),
            $provider,
            $redirectUri,
            $challenge
        );
    }

    /**
     * @throws OpenIdConnectException
     * @throws AccessTokenNotFoundException
     */
    public function fetchAccessTokenWithAuthorizationCode(
        AuthorizationCodeResponse $authorizationCodeResponse
    ): AccessTokenResponse {
        $tokenRequestor = new TokenRequestor(
            $authorizationCodeResponse->getProvider(),
            $authorizationCodeResponse->getRedirectUri(),
            $authorizationCodeResponse->getChallenge(),
            $this->logger
        );

        $accessToken = $tokenRequestor->getAccessTokenForAuthorizationCode(
            $authorizationCodeResponse->getAuthorizationCode()
        );

        return new AccessTokenResponse($accessToken, $authorizationCodeResponse->getProvider());
    }

    public function fetchUserInformationWithAccessToken(AccessTokenResponse $accessTokenResponse): UserInfo
    {
        // TODO:: We will be replacing this call and instead be parsing the JWT.
        return RequestUserInfo::execute(
            $accessTokenResponse->getProvider(),
            $accessTokenResponse->getAccessToken(),
            $this->logger
        );
    }
}
