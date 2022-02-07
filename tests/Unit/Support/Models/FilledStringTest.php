<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Support\Models;

use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Support\Exceptions\EmptyStringException;
use Pinnacle\OpenIdConnect\Support\Models\FilledString;

class FilledStringTest extends TestCase
{
    /**
     * @test
     */
    public function construct_EmptyString_ThrowsException(): void
    {
        // Assert
        $this->expectException(EmptyStringException::class);

        // Act
        new FilledString('');
    }

    /**
     * @test
     */
    public function construct_StringWithEmptySpace_ThrowsException(): void
    {
        // Assert
        $this->expectException(EmptyStringException::class);

        // Act
        new FilledString(' ');
    }

    /**
     * @test
     */
    public function construct_ValidString_getValueReturnsValue(): void
    {
        // Assert
        $nonEmptyString = new FilledString('foo');

        // Act
        $this->assertEquals('foo', $nonEmptyString->getValue());
    }
}
