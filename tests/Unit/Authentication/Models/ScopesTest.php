<?php

namespace Unit\Authentication\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\Scope;
use Pinnacle\OpenIdConnect\Authentication\Models\Scopes;

class ScopesTest extends TestCase
{
    /**
     * @test
     */
    public function construct_CreatesScopesWithDefaults(): void
    {
        // Assemble
        $scopes = new Scopes();

        // Assert
        $this->assertEquals('openid', $scopes->getScopesAsString());
    }

    /**
     * @test
     */
    public function addScope_RandomScope_ExpectDefaultWithNewScope(): void
    {
        // Assemble
        $scopes = new Scopes();

        // Act
        $scopes->addScope(new Scope('foo'));

        // Assert
        $this->assertEquals('openid foo', $scopes->getScopesAsString());
    }

    /**
     * @test
     */
    public function addScope_MultipleScopes_ExpectDefaultWithNewScope(): void
    {
        // Assemble
        $scopes = new Scopes();

        // Act
        $scopes->addScope(new Scope('foo'));
        $scopes->addScope(new Scope('bar'));

        // Assert
        $this->assertEquals('openid foo bar', $scopes->getScopesAsString());
    }
}
