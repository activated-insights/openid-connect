<?php

namespace Unit\Authentication\Constants;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Authentication\Constants\StateKey;
use Pinnacle\OpenIdConnect\Authentication\Models\State;

class StateKeyTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider stateKeysProvider
     */
    public function withPrefix_VariousStateKeys_ReturnsExpectedValue(
        StateKey $stateKey,
        string   $prefix,
        string   $expectedValue
    ): void {
        // Act
        $prefixedKey = $stateKey->withPrefix($prefix);

        // Assert
        $this->assertEquals($expectedValue, $prefixedKey);
    }

    public function stateKeysProvider(): array
    {
        $testCases = [];

        foreach (StateKey::values() as $stateKey) {
            $state = State::createWithRandomString();

            $testCases[] =
                [
                    'stateKey'      => $stateKey,
                    'prefix'        => $state->getValue(),
                    'expectedValue' => $state->getValue() . '.' . $stateKey->getValue(),
                ];
        }

        return $testCases;
    }
}
