<?php

namespace Pinnacle\OpenIdConnect\Tests\Unit\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Stringable;

class TestLogger extends AbstractLogger
{
    public string $latestLevel   = '';

    public string $latestMessage = '';

    /**
     * @throws InvalidArgumentException
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->latestLevel   = $level;
        $this->latestMessage = $message;
    }
}
