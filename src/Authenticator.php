<?php

namespace Pinnacle\OpenIdConnect;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Authentication\AuthenticationUriBuilder;
use Pinnacle\OpenIdConnect\Authentication\Exceptions\ChallengeMismatchException;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Exceptions\StatePersisterMissingValueException;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\StatePersisterWrapper;
use Pinnacle\OpenIdConnect\Authorization\AuthorizationCodeCallbackData;
use Pinnacle\OpenIdConnect\Authorization\AuthorizationCodeResponse;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\AuthorizationCodeCallbackException;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\MissingRequiredQueryParametersException;
use Pinnacle\OpenIdConnect\Authorization\TokensResponse;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Support\Exceptions\InsecureUriException;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\Exceptions\UserIdTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\TokensRequestor;
use Psr\Log\LoggerInterface;

class Authenticator
{
    public function __construct(
        private StatePersisterInterface $statePersister,
        private ?LoggerInterface        $logger = null
    ) {
    }

    /**
     * @throws InsecureUriException
     * @throws OpenIdConnectException
     */
    public function beginAuthentication(
        Uri                            $redirectUri,
        ProviderConfigurationInterface $provider,
    ): AuthenticationUriBuilder {
        if ($redirectUri->getScheme() !== 'https') {
            throw new InsecureUriException('Redirect URI must use https.');
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
     * @throws AuthorizationCodeCallbackException
     * @throws ChallengeMismatchException
     * @throws StatePersisterMissingValueException
     */
    public function handleAuthorizationCodeCallback(Uri $callbackUri): AuthorizationCodeResponse
    {
        $callbackData = new AuthorizationCodeCallbackData($callbackUri);

        $responseState = $callbackData->getState();

        $statePersisterWrapper = new StatePersisterWrapper($this->statePersister, $responseState);

        $challenge   = $statePersisterWrapper->getChallenge();
        $provider    = $statePersisterWrapper->getProvider();
        $redirectUri = $statePersisterWrapper->getRedirectUri();

        return new AuthorizationCodeResponse(
            $callbackData->getAuthorizationCode(),
            $provider,
            $redirectUri,
            $challenge
        );
    }

    /**
     * @throws OpenIdConnectException
     * @throws AccessTokenNotFoundException
     * @throws UserIdTokenNotFoundException
     */
    public function fetchTokensWithAuthorizationCode(
        AuthorizationCodeResponse $authorizationCodeResponse
    ): TokensResponse {
        $tokenRequestor = new TokensRequestor(
            $authorizationCodeResponse->getProvider(),
            $authorizationCodeResponse->getRedirectUri(),
            $authorizationCodeResponse->getChallenge(),
            $this->logger
        );

        $tokens = $tokenRequestor->fetchTokensForAuthorizationCode(
            $authorizationCodeResponse->getAuthorizationCode(),
        );

        return new TokensResponse($tokens, $authorizationCodeResponse->getProvider());
    }
}
