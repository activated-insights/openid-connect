<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect\Exceptions;

use DomainException;

/**
 * An exception thrown when OAuth authentication fails.
 */
class OAuthFailedException extends DomainException
{
}
