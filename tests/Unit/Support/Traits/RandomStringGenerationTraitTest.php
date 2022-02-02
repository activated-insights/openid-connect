<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Support\Traits;

use Exception;
use PHPUnit\Framework\TestCase;
use Pinnacle\OpenIdConnect\Support\Traits\RandomStringGenerationTrait;

class RandomStringGenerationTraitTest extends TestCase
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function generateRandomString_WithRandomLength_ReturnsExpectedLength(): void
    {
        $class = $this->generateClass();

        $length = random_int(1, 100);

        $randomString = $class->generate($length);

        $this->assertEquals($length, strlen($randomString));
    }

    private function generateClass(): object
    {
        return new class {
            use RandomStringGenerationTrait;

            public function generate(int $value): string
            {
                return $this->generateRandomString($value);
            }
        };
    }
}
