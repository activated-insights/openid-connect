<?php

namespace Pinnacle\OpenIdConnect\Models;

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Models\Contracts\ProviderInterface;

class AuthenticationUriBuilder
{
    private const                 DEFAULT_SCOPES        = ['openid', 'profile', 'email'];

    private const                 CODE_CHALLENGE_METHOD = 'S256';

    private const                 RESPONSE_TYPE         = 'code';

    /**
     * @var string[]
     */
    private array  $scopes;

    private string $state;

    private string $codeChallenge;

    public function __construct(private ProviderInterface $provider, private Uri $redirectUri)
    {
        $this->scopes        = self::DEFAULT_SCOPES;
        $this->state         = $this->generateRandomString();
        $this->codeChallenge = $this->generateCodeChallenge();
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCodeChallenge(): string
    {
        return $this->codeChallenge;
    }

    public function withScopes(string ...$scopes): self
    {
        $this->scopes = array_merge($this->scopes, $scopes);

        return $this;
    }

    public function uri(): Uri
    {
        return $this->provider->getAuthorizationEndpoint()->withQuery(Query::build($this->buildParameters()));
    }

    /**
     * @return array
     */
    private function buildParameters(): array
    {
        return [
            // Use the authorization code flow so that tokens are not exposed to the client browser.
            'response_type'         => self::RESPONSE_TYPE,
            'client_id'             => $this->provider->getClientId(),
            'redirect_uri'          => (string)$this->redirectUri,
            'scope'                 => implode(' ', $this->scopes),
            'state'                 => $this->state,
            'code_challenge_method' => self::CODE_CHALLENGE_METHOD,
            'code_challenge'        => $this->generateCodeChallenge(),
        ];
    }

    private function generateCodeChallenge(): string
    {
        $randomString  = $this->generateRandomString(64);
        $binaryHash    = hash('sha256', $randomString, true);
        $base64Encoded = base64_encode($binaryHash);

        // Convert from standard Base64 encoding to Base64Url encoding.
        return rtrim(strtr($base64Encoded, '+/', '-_'), '=');
    }

    private function generateRandomString($length = 16): string
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
