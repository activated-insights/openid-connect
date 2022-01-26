<?php

namespace Unit\Authentication\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\State;

class StateTest extends TestCase
{
    /**
     * @test
     */
    public function createWithRandomString_CreatesSixteenCharacterString(): void
    {
        // Act
        $state = State::createWithRandomString();

        // Assert
        $this->assertEquals(16, strlen($state->getValue()));
    }
}
