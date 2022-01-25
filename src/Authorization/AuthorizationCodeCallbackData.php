<?php

namespace Pinnacle\OpenIdConnect\Authorization;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authorization\Constants\AuthorizationCodeCallbackKey;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\AuthorizationCodeCallbackException;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\MissingRequiredQueryParametersException;

class AuthorizationCodeCallbackData
{
    /**
     * @var string[]
     */
    private array   $rawQueryParams;

    private ?string $authorizationCode = null;

    private ?string $stateValue        = null;

    private ?string $challengeValue    = null;

    private ?string $errorCode         = null;

    private ?string $errorDescription  = null;

    /**
     * @throws MissingRequiredQueryParametersException
     * @throws AuthorizationCodeCallbackException
     */
    public function __construct(Uri $callbackUri)
    {
        $this->parseQueryParameters($callbackUri);

        $this->assertWithoutError();

        $this->assertHasRequiredParameters();
    }

    public function getAuthorizationCode(): string
    {
        if ($this->authorizationCode === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CODE());
        }

        return $this->authorizationCode;
    }

    public function getState(): State
    {
        if ($this->stateValue === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::STATE());
        }

        return new State($this->stateValue);
    }

    public function getChallenge(): Challenge
    {
        if ($this->challengeValue === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CHALLENGE());
        }

        return new Challenge($this->challengeValue);
    }

    /**
     * @param Uri $callbackUri
     *
     * @return void
     */
    private function parseQueryParameters(Uri $callbackUri): void
    {
        $this->rawQueryParams = [];
        parse_str($callbackUri->getQuery(), $this->rawQueryParams);

        $this->authorizationCode = $this->findQueryParameter(AuthorizationCodeCallbackKey::CODE());
        $this->stateValue        = $this->findQueryParameter(AuthorizationCodeCallbackKey::STATE());
        $this->challengeValue    = $this->findQueryParameter(AuthorizationCodeCallbackKey::CHALLENGE());
        $this->errorCode         = $this->findQueryParameter(AuthorizationCodeCallbackKey::ERROR());
        $this->errorDescription  = $this->findQueryParameter(AuthorizationCodeCallbackKey::ERROR_DESCRIPTION());
    }

    private function findQueryParameter(AuthorizationCodeCallbackKey $parameterKey): ?string
    {
        if (isset($this->rawQueryParams[$parameterKey->getValue()])) {
            return $this->rawQueryParams[$parameterKey->getValue()];
        }

        return null;
    }

    private function assertWithoutError(): void
    {
        if ($this->errorCode !== null) {
            throw new AuthorizationCodeCallbackException(
                $this->errorCode,
                $this->errorDescription
            );
        }
    }

    /**
     * @throws MissingRequiredQueryParametersException
     */
    private function assertHasRequiredParameters()
    {
        if ($this->authorizationCode === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CODE());
        }

        if ($this->stateValue === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::STATE());
        }

        if ($this->challengeValue === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CHALLENGE());
        }
    }
}
