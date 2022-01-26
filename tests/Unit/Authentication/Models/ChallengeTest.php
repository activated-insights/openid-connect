<?php

namespace Unit\Authentication\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;

class ChallengeTest extends TestCase
{
    /**
     * @test
     */
    public function createWithRandomString_functionCall_Generates43CharacterString(): void
    {
        // Act
        $challenge = Challenge::createWithRandomString();

        // Assert
        $this->assertEquals(64, strlen($challenge->getValue()));
    }

    /**
     * @test
     */
    public function hash_ReturnsExpectedStringLength(): void
    {
        // Assemble
        $challenge = Challenge::createWithRandomString();

        // Act
        $output = $challenge->hash();

        // Assert
        $this->assertEquals(43, strlen($output));
    }
}
