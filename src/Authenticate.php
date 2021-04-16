<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect;

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenidConnect\Dtos\ProviderDto;
use Pinnacle\OpenidConnect\Enums\ChallengeMethod;
use Pinnacle\OpenidConnect\Enums\IdentityProvider;
use Pinnacle\OpenidConnect\Enums\ResponseType;

class Authenticate
{
    private ProviderDto  $providerDto;

    private ResponseType $responseType;

    private Uri          $redirectUri;

    private ?string      $state            = null;

    private ?string      $identityProvider = null;

    private ?string      $idpIdentifier    = null;

    /**
     * @var string[]
     */
    private array            $scopes          = [];

    private ?ChallengeMethod $challengeMethod = null;

    private ?string          $challenge       = null;

    public function __construct(ProviderDto $providerDto, ResponseType $responseType, Uri $redirectUri)
    {
        $this->providerDto  = $providerDto;
        $this->responseType = $responseType;
        $this->redirectUri  = $redirectUri;
    }

    public function getAuthRedirectUrl(): Uri
    {
        $parameters = $this->buildParameters();

        return $this->providerDto
            ->getAuthorizationEndpoint()
            ->withQuery(Query::build($parameters));
    }

    public function withState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @param IdentityProvider|string $identityProvider
     */
    public function withIdentityProvider($identityProvider): self
    {
        $this->identityProvider = $identityProvider;

        return $this;
    }

    public function withIdpIdentifier(?string $idpIdentifier): self
    {
        $this->idpIdentifier = $idpIdentifier;

        return $this;
    }

    /**
     * @param [string|string[]] $value
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

    public function withChallenge(ChallengeMethod $challengeMethod, string $challenge): self
    {
        $this->challengeMethod = $challengeMethod;
        $this->challenge       = $challenge;

        return $this;
    }

    /**
     * @return mixed[]
     */
    private function buildParameters(): array
    {
        $parameters = [
            // Use the authorization code flow so that tokens are not exposed to the client browser.
            'response_type' => $this->responseType->getValue(),
            'client_id'     => $this->providerDto->getClientId(),
            'redirect_uri'  => (string)$this->redirectUri,
        ];

        if (count($this->scopes) > 0) {
            $parameters['scope'] = implode(' ', $this->scopes);
        }

        if ($this->state !== null) {
            $parameters['state'] = $this->state;
        }

        if ($this->challengeMethod !== null) {
            $parameters['code_challenge_method'] = $this->challengeMethod->getValue();
        }

        if ($this->challenge !== null) {
            $parameters['code_challenge'] = $this->challenge;
        }

        if ($this->identityProvider !== null) {
            $parameters['identity_provider'] = $this->identityProvider;
        }

        if ($this->idpIdentifier !== null) {
            $parameters['idp_identifier'] = $this->idpIdentifier;
        }

        return $parameters;
    }
}
