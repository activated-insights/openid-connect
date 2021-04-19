<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect;

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Dtos\ProviderDto;
use Pinnacle\OpenIdConnect\Exceptions\OAuthFailedException;

class Authenticate
{
    private ProviderDto $providerDto;

    private string      $responseType;

    private Uri         $redirectUri;

    private string      $state;

    /**
     * @var string[]
     */
    private array  $scopes;

    private string $challengeMethod;

    private string $challenge;

    /**
     * @param Uri|string $redirectUri
     */
    public function __construct(
        ProviderDto $providerDto,
        $redirectUri
    ) {
        $this->providerDto  = $providerDto;
        $this->redirectUri  = $redirectUri instanceof Uri ? $redirectUri : new Uri($redirectUri);

        if($this->redirectUri->getScheme() !== 'https') {
            throw new OAuthFailedException('Redirect URI must use https');
        }

        // Default values
        $this->responseType = 'code';
        $this->state           = self::generateRandomString();
        $this->scopes          = ['openid', 'profile', 'email'];
        $this->challengeMethod = 'S256';
        $this->challenge       = self::generateRandomString(64);
    }

    public function getAuthRedirectUrl(): Uri
    {
        $parameters = $this->buildParameters();

        return $this->providerDto
            ->getAuthorizationEndpoint()
            ->withQuery(Query::build($parameters));
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getChallenge(): string
    {
        return $this->challenge;
    }

    /**
     * @param [string|string[]] $value openid, profile, and email scopes are already added.
     */
    public function addScope($scope): self
    {
        if (is_array($scope)) {
            $this->scopes = [...$this->scopes, ...$scope];
        } else {
            $this->scopes = [...$this->scopes, $scope];
        }

        return $this;
    }

    /**
     * @return mixed[]
     */
    private function buildParameters(): array
    {
        $scopes = array_unique($this->scopes);

        return [
            // Use the authorization code flow so that tokens are not exposed to the client browser.
            'response_type'         => $this->responseType,
            'client_id'             => $this->providerDto->getClientId(),
            'redirect_uri'          => (string)$this->redirectUri,
            'scope'                 => implode(' ', $scopes),
            'state'                 => $this->state,
            'code_challenge_method' => $this->challengeMethod,
            'code_challenge'        => $this->parseChallengeAsParameterString(),
        ];
    }

    private function parseChallengeAsParameterString(): string
    {
        $binaryHash    = hash('sha256', $this->challenge, true);
        $base64Encoded = base64_encode($binaryHash);

        // Convert from standard Base64 encoding to Base64Url encoding.
        return rtrim(strtr($base64Encoded, '+/', '-_'), '=');
    }

    /**
     *
     */
    private static function generateRandomString($length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
