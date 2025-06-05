<?php

namespace Pinnacle\OpenIdConnect\Authentication\Models;

class Scopes
{
    private const                 DEFAULT_SCOPES = ['openid'];

    /**
     * @var Scope[]
     */
    private array $scopes;

    public function __construct()
    {
        $this->scopes = [];

        foreach (self::DEFAULT_SCOPES as $defaultScope) {
            $this->addScope(new Scope($defaultScope));
        }
    }

    public function addScope(Scope $scope): void
    {
        $this->scopes[] = $scope;
    }

    public function removeScope(Scope $scope): void
    {
        $this->scopes = array_filter($this->scopes, function (Scope $existingScope) use ($scope) {
            return $existingScope->getValue() !== $scope->getValue();
        });
    }

    public function getScopesAsString(): string
    {
        $scopeValues = [];

        foreach ($this->scopes as $scope) {
            $scopeValues[] = $scope->getValue();
        }

        return implode(' ', $scopeValues);
    }
}
