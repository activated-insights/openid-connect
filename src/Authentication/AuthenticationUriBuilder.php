<?php

namespace Pinnacle\OpenIdConnect\Authentication;

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\Scope;
use Pinnacle\OpenIdConnect\Authentication\Models\Scopes;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;

class AuthenticationUriBuilder
{
    private const CODE_CHALLENGE_METHOD = 'S256';

    private const RESPONSE_TYPE         = 'code';

    private Scopes    $scopes;

    private State     $state;

    private Challenge $codeChallenge;

    /**
     * @throws OpenIdConnectException
     */
    public function __construct(private ProviderConfigurationInterface $provider, private Uri $redirectUri)
    {
        $this->scopes        = new Scopes();
        $this->state         = State::createWithRandomString();
        $this->codeChallenge = Challenge::createWithRandomString();
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function getCodeChallenge(): Challenge
    {
        return $this->codeChallenge;
    }

    public function withScopes(string ...$scopes): self
    {
        foreach ($scopes as $scope) {
            $this->scopes->addScope(new Scope($scope));
        }

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
            'client_id'             => $this->provider->getClientId()->getValue(),
            'redirect_uri'          => (string)$this->redirectUri,
            'scope'                 => $this->scopes->getScopesAsString(),
            'state'                 => $this->state->getValue(),
            'code_challenge_method' => self::CODE_CHALLENGE_METHOD,
            'code_challenge'        => $this->codeChallenge->hash(),
        ];
    }
}
