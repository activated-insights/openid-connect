<?php

namespace Pinnacle\OpenIdConnect\Services;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Exceptions\StatePersisterMissingValueException;
use Pinnacle\OpenIdConnect\Models\Constants\StateKey;
use Pinnacle\OpenIdConnect\Models\Contracts\ProviderInterface;
use Pinnacle\OpenIdConnect\Models\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Models\Provider;

class StatePersisterWrapper
{
    public function __construct(private StatePersisterInterface $statePersister, private string $stateKey)
    {
    }

    public function storeChallenge(string $challenge): void
    {
        $this->storeValueWithKey(StateKey::CHALLENGE(), $challenge);
    }

    /**
     * @throws StatePersisterMissingValueException
     */
    public function getChallenge(): string
    {
        $challenge = $this->statePersister->getValue(StateKey::CHALLENGE()->withPrefix($this->stateKey));

        if ($challenge === null) {
            throw new StatePersisterMissingValueException('Unable to retrieve challenge from state store.');
        }

        return $challenge;
    }

    public function storeProvider(ProviderInterface $provider): void
    {
        $this->storeValueWithKey(StateKey::PROVIDER_IDENTIFIER(), $provider->getIdentifier());
        $this->storeValueWithKey(StateKey::PROVIDER_CLIENT_ID(), $provider->getClientId());
        $this->storeValueWithKey(StateKey::PROVIDER_CLIENT_SECRET(), $provider->getClientSecret());
        $this->storeValueWithKey(StateKey::PROVIDER_AUTHORIZATION_ENDPOINT(), $provider->getAuthorizationEndpoint());
        $this->storeValueWithKey(StateKey::PROVIDER_TOKEN_ENDPOINT(), $provider->getTokenEndpoint());
        $this->storeValueWithKey(StateKey::PROVIDER_USER_INFO_ENDPOINT(), $provider->getUserInfoEndpoint());
    }

    /**
     * @throws StatePersisterMissingValueException
     */
    public function getProvider(): Provider
    {
        $identifier            = $this->getValueWithStateKey(StateKey::PROVIDER_IDENTIFIER());
        $clientId              = $this->getValueWithStateKey(StateKey::PROVIDER_CLIENT_ID());
        $clientSecret          = $this->getValueWithStateKey(StateKey::PROVIDER_CLIENT_SECRET());
        $authorizationEndpoint = $this->getValueWithStateKey(StateKey::PROVIDER_AUTHORIZATION_ENDPOINT());
        $tokenEndpoint         = $this->getValueWithStateKey(StateKey::PROVIDER_TOKEN_ENDPOINT());
        $userInfoEndpoint      = $this->getValueWithStateKey(StateKey::PROVIDER_USER_INFO_ENDPOINT());

        if ($clientId === null ||
            $clientSecret === null ||
            $authorizationEndpoint === null ||
            $tokenEndpoint === null ||
            $userInfoEndpoint === null
        ) {
            throw new StatePersisterMissingValueException('Unable to retrieve the provider from state store.');
        }

        return new Provider(
            $identifier,
            $clientId,
            $clientSecret,
            $authorizationEndpoint,
            $tokenEndpoint,
            $userInfoEndpoint
        );
    }

    public function storeRedirectUri(Uri $redirectUri): void
    {
        $this->storeValueWithKey(StateKey::REDIRECT_URI(), (string)$redirectUri);
    }

    public function getRedirectUri(): Uri
    {
        $redirectUriValue = $this->getValueWithStateKey(StateKey::REDIRECT_URI());

        if ($redirectUriValue === null) {
            throw new StatePersisterMissingValueException('Unable to retrieve redirect URI from state store.');
        }

        return new Uri($redirectUriValue);
    }

    private function storeValueWithKey(StateKey $stateKey, mixed $value): void
    {
        $this->statePersister->storeValue($stateKey->withPrefix($this->stateKey), $value);
    }

    private function getValueWithStateKey(StateKey $stateKey)
    {
        return $this->statePersister->getValue($stateKey->withPrefix($this->stateKey));
    }
}
