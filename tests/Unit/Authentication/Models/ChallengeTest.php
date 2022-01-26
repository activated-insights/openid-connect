<?php

namespace Unit\Authentication\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;

class ChallengeTest extends TestCase
{
    /**
     * @test
     */
    public function createWithRandomChallenge_functionCall_Generates43CharacterString(): void
    {
        // Act
        $challenge = Challenge::createWithRandomChallenge();

        // Assert
        $this->assertEquals(43, strlen($challenge->getValue()));
    }

    /**
     * @test
     */
    public function equals_TwoEqualChallenges_ExpectTrue(): void
    {
        // Assemble
        $challengeOne = Challenge::createWithRandomChallenge();
        $challengeTwo = new Challenge($challengeOne->getValue());

        // Act
        $output = $challengeOne->equals($challengeTwo);

        // Assert
        $this->assertTrue($output);
    }

    /**
     * @test
     */
    public function equals_TwoInEqualChallenges_ExpectFalse(): void
    {
        // Assemble
        $challengeOne = Challenge::createWithRandomChallenge();
        $challengeTwo = Challenge::createWithRandomChallenge();

        // Act
        $output = $challengeOne->equals($challengeTwo);

        // Assert
        $this->assertFalse($output);
    }

    /**
     * @test
     */
    public function equals_WithNull_ExpectFalse(): void
    {
        // Assemble
        $challenge = Challenge::createWithRandomChallenge();

        // Act
        $output = $challenge->equals(null);

        // Assert
        $this->assertFalse($output);
    }
}
