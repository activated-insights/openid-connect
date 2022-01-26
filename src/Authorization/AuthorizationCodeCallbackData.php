<?php

namespace Pinnacle\OpenIdConnect\Authorization;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authentication\Models\State;
use Pinnacle\OpenIdConnect\Authorization\Constants\AuthorizationCodeCallbackKey;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\AuthorizationCodeCallbackException;
use Pinnacle\OpenIdConnect\Authorization\Exceptions\MissingRequiredQueryParametersException;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;

class AuthorizationCodeCallbackData
{
    /**
     * @var string[]
     */
    private array              $rawQueryParams;

    private ?AuthorizationCode $authorizationCode = null;

    private ?State             $state             = null;

    private ?Challenge         $challenge         = null;

    private ?string            $errorCodeValue    = null;

    private ?string            $errorDescription  = null;

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

    /**
     * @throws MissingRequiredQueryParametersException
     */
    public function getAuthorizationCode(): AuthorizationCode
    {
        if ($this->authorizationCode === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::CODE());
        }

        return $this->authorizationCode;
    }

    /**
     * @throws MissingRequiredQueryParametersException
     */
    public function getState(): State
    {
        if ($this->state === null) {
            throw new MissingRequiredQueryParametersException(AuthorizationCodeCallbackKey::STATE());
        }

        return $this->state;
    }

    /**
     * @throws MissingRequiredQueryParametersException
     */
    public function getChallenge(): Challenge
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

        $codeValue               = $this->findQueryParameter(AuthorizationCodeCallbackKey::CODE());
        $this->authorizationCode = $codeValue !== null ? new AuthorizationCode($codeValue) : null;

        $stateValue  = $this->findQueryParameter(AuthorizationCodeCallbackKey::STATE());
        $this->state = $stateValue !== null ? new State($stateValue) : null;

        $challengeValue  = $this->findQueryParameter(AuthorizationCodeCallbackKey::CHALLENGE());
        $this->challenge = $challengeValue !== null ? new Challenge($challengeValue) : null;

        $this->errorCodeValue   = $this->findQueryParameter(AuthorizationCodeCallbackKey::ERROR());
        $this->errorDescription = $this->findQueryParameter(AuthorizationCodeCallbackKey::ERROR_DESCRIPTION());
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
        if ($this->errorCodeValue !== null) {
            throw new AuthorizationCodeCallbackException(
                $this->errorCodeValue,
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
