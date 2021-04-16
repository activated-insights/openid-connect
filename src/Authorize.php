<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenidConnect\Dtos\ProviderDto;
use Pinnacle\OpenidConnect\Dtos\UserInfoDto;
use Pinnacle\OpenidConnect\Services\RequestAccessToken;
use Pinnacle\OpenidConnect\Services\RequestUserInfo;

class Authorize
{
    private ProviderDto  $providerDto;

    private Uri          $redirectUri;

    private string       $authorizationCode;

    private ?string      $codeVerifier;

    private ?string      $accessToken = null;

    private ?UserInfoDto $userInfo    = null;

    /**
     * State nonce verification should happen prior to this
     *
     * @param Uri|string  $redirectUri
     * @param string|null $codeVerifier Only used if PKCE was used initially.
     */
    public function __construct(
        ProviderDto $providerDto,
        $redirectUri,
        string $authorizationCode,
        ?string $codeVerifier = null
    ) {
        $this->providerDto       = $providerDto;
        $this->redirectUri       = $redirectUri instanceof Uri ? $redirectUri : new Uri($redirectUri);
        $this->authorizationCode = $authorizationCode;
        $this->codeVerifier      = $codeVerifier;
    }

    /**
     * Any verification for state nonce should be done prior to requesting authorization
     */
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

    /**
     * Any verification for state nonce should be done prior to requesting information
     */
    public function getUserInfo(): UserInfoDto
    {
        if ($this->userInfo === null) {
            $this->userInfo = RequestUserInfo::execute($this->providerDto, $this->getAccessToken());
        }

        return $this->userInfo;
    }
}
