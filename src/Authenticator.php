<?php

namespace Pinnacle\OpenIdConnect;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Exceptions\InsecureUriProtocolException;
use Pinnacle\OpenIdConnect\Exceptions\MismatchStateException;
use Pinnacle\OpenIdConnect\Models\AuthenticationRequest;
use Pinnacle\OpenIdConnect\Models\Contracts\AuthenticationResponseInterface;
use Pinnacle\OpenIdConnect\Models\Contracts\ProviderInterface;
use Pinnacle\OpenIdConnect\Models\UserInfo;
use Pinnacle\OpenIdConnect\Services\RequestTokens;
use Pinnacle\OpenIdConnect\Services\RequestUserInfo;
use Psr\Log\LoggerInterface;

class Authenticator
{
    public function __construct(
        private ProviderInterface $provider,
        private ?LoggerInterface  $logger = null
    ) {
    }

    /**
     * @param string[] $scopes
     *
     * @return AuthenticationRequest
     */
    public function buildAuthenticationRequest(Uri $redirectUri, array $scopes): AuthenticationRequest
    {
        if ($redirectUri->getScheme() !== 'https') {
            throw new InsecureUriProtocolException('Redirect URI must use https');
        }

        $authenticationRequest = new AuthenticationRequest($this->provider, $redirectUri);

        foreach ($scopes as $scope) {
            $authenticationRequest->addScope($scope);
        }

        return $authenticationRequest;
    }

    public function getUserInformationFromAuthenticationResponse(
        AuthenticationResponseInterface $response,
        AuthenticationRequest           $originalRequest
    ): UserInfo {
        if ($response->getState() !== $originalRequest->getState()) {
            throw new MismatchStateException(
                sprintf(
                    'Response state %s does not match the request state %s',
                    $response->getState(),
                    $originalRequest->getState()
                )
            );
        }

        $requestTokens = new RequestTokens(
            $this->provider,
            $originalRequest->getRedirectUri(),
            $originalRequest->getCodeChallenge(),
            $this->logger
        );

        $accessToken = $requestTokens->getAccessTokenForAuthorizationCode(
            $response->getAuthorizationCode()
        );

        // TODO:: We will be replacing this call and instead be parsing the JWT.
        return RequestUserInfo::execute($this->provider, $accessToken, $this->logger);
    }
}
