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
use Pinnacle\OpenIdConnect\Tokens\Exceptions\AccessTokenNotFoundException;
use Pinnacle\OpenIdConnect\Tokens\TokenRequestor;
use Pinnacle\OpenIdConnect\Support\Exceptions\InsecureUriException;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\UserInfo\Models\UserInfo;
use Pinnacle\OpenIdConnect\UserInfo\RequestUserInfo;
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

        if (!$callbackData->getChallenge()->equals($challenge)) {
            throw new ChallengeMismatchException(
                sprintf(
                    'Response challenge %s does not match the original request %s.',
                    $callbackData->getChallenge()->getValue(),
                    $challenge->getValue()
                )
            );
        }

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
     */
    public function fetchTokensWithAuthorizationCode(
        AuthorizationCodeResponse $authorizationCodeResponse
    ): TokensResponse {
        $tokenRequestor = new TokenRequestor(
            $authorizationCodeResponse->getProvider(),
            $authorizationCodeResponse->getRedirectUri(),
            $authorizationCodeResponse->getChallenge(),
            $this->logger
        );

        //TODO:: This will eventually return an object containing all the tokens. Currently it only returns the access token.
        $accessToken = $tokenRequestor->fetchTokensForAuthorizationCode(
            $authorizationCodeResponse->getAuthorizationCode()
        );

        return new TokensResponse($accessToken, $authorizationCodeResponse->getProvider());
    }

    public function fetchUserInformationWithAccessToken(TokensResponse $authenticationTokensResponse
    ): UserInfo {
        // TODO:: We will be replacing this call and instead be parsing the JWT.
        return RequestUserInfo::execute(
            $authenticationTokensResponse->getProvider(),
            $authenticationTokensResponse->getAccessToken(),
            $this->logger
        );
    }
}
