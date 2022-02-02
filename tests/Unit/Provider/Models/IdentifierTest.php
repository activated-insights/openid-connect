<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Provider\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;
use Pinnacle\OpenIdConnect\Support\Exceptions\EmptyStringException;

class IdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function construct_withEmptyString_ThrowsExpectedException(): void
    {
        // Assert
        $this->expectException(EmptyStringException::class);

        // Act
        new Identifier('');
    }

    /**
     * @test
     */
    public function construct_StringWithEmptySpace_ThrowsExpectedException(): void
    {
        // Assert
        $this->expectException(EmptyStringException::class);

        // Act
        new Identifier('   ');
    }

    /**
     * @test
     */
    public function getValue_WithString_ReturnsString(): void
    {
        // Act
        $value = (new Identifier('string'))->getValue();

        // Assert
        $this->assertSame('string', $value);
    }

    /**
     * @test
     */
    public function getValue_WithInteger_ReturnsInteger(): void
    {
        // Act
        $value = (new Identifier(1))->getValue();

        // Assert
        $this->assertSame(1, $value);
    }
}
