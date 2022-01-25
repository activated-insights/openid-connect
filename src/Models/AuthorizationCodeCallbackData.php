<?php

namespace Pinnacle\OpenIdConnect\Models;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Exceptions\AuthorizationCodeCallbackException;
use Pinnacle\OpenIdConnect\Exceptions\MissingRequiredQueryParametersException;
use Pinnacle\OpenIdConnect\Models\Constants\AuthorizationCodeCallbackKey;

class AuthorizationCodeCallbackData
{
    /**
     * @var string[]
     */
    private array   $rawQueryParams;

    private ?string $authorizationCode = null;

    private ?string $state             = null;

    private ?string $challenge         = null;

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

    public function getState(): string
    {
        if ($this->state === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::STATE());
        }

        return $this->state;
    }

    public function getChallenge(): string
    {
        if ($this->challenge === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CHALLENGE());
        }

        return $this->challenge;
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
        $this->state             = $this->findQueryParameter(AuthorizationCodeCallbackKey::STATE());
        $this->challenge         = $this->findQueryParameter(AuthorizationCodeCallbackKey::CHALLENGE());
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

        if ($this->state === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::STATE());
        }

        if ($this->challenge === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CHALLENGE());
        }
    }
}
