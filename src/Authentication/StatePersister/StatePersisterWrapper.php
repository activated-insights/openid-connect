<?php

namespace Pinnacle\OpenIdConnect\Authentication\StatePersister;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Authentication\Constants\StateKey;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Contracts\StatePersisterInterface;
use Pinnacle\OpenIdConnect\Authentication\StatePersister\Exceptions\StatePersisterMissingValueException;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;

class StatePersisterWrapper
{
    public function __construct(private StatePersisterInterface $statePersister, private State $stateKey)
    {
    }

    public function storeChallenge(Challenge $challenge): void
    {
        $this->storeValueWithKey(StateKey::CHALLENGE(), $challenge->getValue());
    }

    /**
     * @throws StatePersisterMissingValueException
     */
    public function getChallenge(): Challenge
    {
        $challengeValue = $this->statePersister->getValue(
            StateKey::CHALLENGE()->withPrefix($this->stateKey->getValue())
        );

        if ($challengeValue === null) {
            throw new StatePersisterMissingValueException('Unable to retrieve challenge from state store.');
        }

        return new Challenge($challengeValue);
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

        if ($clientIdValue === null ||
            $clientSecretValue === null ||
            $authorizationEndpointValue === null ||
            $tokenEndpointValue === null
        ) {
            throw new StatePersisterMissingValueException('Unable to retrieve the provider from state store.');
        }

        return new ProviderConfiguration(
            $identifierValue !== null ? new Identifier($identifierValue) : null,
            new ClientId($clientIdValue),
            new ClientSecret($clientSecretValue),
            new Uri($authorizationEndpointValue),
            new Uri($tokenEndpointValue)
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
        $this->statePersister->storeValue($stateKey->withPrefix($this->stateKey->getValue()), $value);
    }

    private function getValueWithStateKey(StateKey $stateKey)
    {
        return $this->statePersister->getValue($stateKey->withPrefix($this->stateKey->getValue()));
    }
}
