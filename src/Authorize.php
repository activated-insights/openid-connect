<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Dtos\ProviderDto;
use Pinnacle\OpenIdConnect\Dtos\UserInfoDto;
use Pinnacle\OpenIdConnect\Exceptions\OAuthFailedException;
use Pinnacle\OpenIdConnect\Services\RequestAccessToken;
use Pinnacle\OpenIdConnect\Services\RequestUserInfo;

class Authorize
{
    private ProviderDto  $providerDto;

    private Uri          $redirectUri;

    private string       $authorizationCode;

    private ?string      $codeVerifier;

    private ?string      $accessToken = null;

    private ?UserInfoDto $userInfo    = null;

    /**
     * @param Uri|string $redirectUri
     * @param string     $savedState    The state saved in the session
     * @param string     $returnedState The state returned in the callback query
     */
    public function __construct(
        ProviderDto $providerDto,
        $redirectUri,
        string $authorizationCode,
        string $savedState,
        string $returnedState,
        string $codeVerifier
    ) {
        $this->providerDto = $providerDto;
        $this->redirectUri = $redirectUri instanceof Uri ? $redirectUri : new Uri($redirectUri);
        $this->authorizationCode = $authorizationCode;
        $this->codeVerifier      = $codeVerifier;

        if ($this->redirectUri->getScheme() !== 'https') {
            throw new OAuthFailedException('Redirect URI must use https');
        }

        if ($savedState !== $returnedState) {
            throw new OAuthFailedException('State mismatch');
        }
    }

    public function getAccessToken(): string
    {
        if ($this->accessToken === null) {
            $this->accessToken = RequestAccessToken::execute(
                $this->providerDto,
                $this->redirectUri,
                $this->authorizationCode,
                $this->codeVerifier
            );
        }

        return $this->accessToken;
    }

    public function getUserInfo(): UserInfoDto
    {
        if ($this->userInfo === null) {
            $this->userInfo = RequestUserInfo::execute($this->providerDto, $this->getAccessToken());
        }

        return $this->userInfo;
    }
}
