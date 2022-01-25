<?php

namespace Pinnacle\OpenIdConnect\State;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Provider\ClientId;
use Pinnacle\OpenIdConnect\Provider\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Provider\Identifier;
use Pinnacle\OpenIdConnect\Provider\ProviderConfiguration;
use Pinnacle\OpenIdConnect\State\Constants\StateKey;
use Pinnacle\OpenIdConnect\State\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\State\Exceptions\StatePersisterMissingValueException;

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

    public function storeProvider(ProviderConfigurationInterface $provider): void
    {
        $this->storeValueWithKey(StateKey::PROVIDER_IDENTIFIER(), $provider->getIdentifier()?->getValue());
        $this->storeValueWithKey(StateKey::PROVIDER_CLIENT_ID(), $provider->getClientId()->getValue());
        $this->storeValueWithKey(StateKey::PROVIDER_CLIENT_SECRET(), $provider->getClientSecret()->getValue());
        $this->storeValueWithKey(
            StateKey::PROVIDER_AUTHORIZATION_ENDPOINT(),
            (string)$provider->getAuthorizationEndpoint()
        );
        $this->storeValueWithKey(StateKey::PROVIDER_TOKEN_ENDPOINT(), (string)$provider->getTokenEndpoint());
        $this->storeValueWithKey(StateKey::PROVIDER_USER_INFO_ENDPOINT(), (string)$provider->getUserInfoEndpoint());
    }

    /**
     * @throws StatePersisterMissingValueException
     */
    public function getProvider(): ProviderConfiguration
    {
        $identifierValue            = $this->getValueWithStateKey(StateKey::PROVIDER_IDENTIFIER());
        $clientIdValue              = $this->getValueWithStateKey(StateKey::PROVIDER_CLIENT_ID());
        $clientSecretValue          = $this->getValueWithStateKey(StateKey::PROVIDER_CLIENT_SECRET());
        $authorizationEndpointValue = $this->getValueWithStateKey(StateKey::PROVIDER_AUTHORIZATION_ENDPOINT());
        $tokenEndpointValue         = $this->getValueWithStateKey(StateKey::PROVIDER_TOKEN_ENDPOINT());
        $userInfoEndpointValue      = $this->getValueWithStateKey(StateKey::PROVIDER_USER_INFO_ENDPOINT());

        if ($clientIdValue === null ||
            $clientSecretValue === null ||
            $authorizationEndpointValue === null ||
            $tokenEndpointValue === null ||
            $userInfoEndpointValue === null
        ) {
            throw new StatePersisterMissingValueException('Unable to retrieve the provider from state store.');
        }

        return new ProviderConfiguration(
            $identifierValue !== null ? new Identifier($identifierValue) : null,
            new ClientId($clientIdValue),
            new ClientSecret($clientSecretValue),
            new Uri($authorizationEndpointValue),
            new Uri($tokenEndpointValue),
            new Uri($userInfoEndpointValue)
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
