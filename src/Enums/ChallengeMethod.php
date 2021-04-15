<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ChallengeMethod PLAIN()
 * @method static ChallengeMethod S256()
 */
class ChallengeMethod extends Enum
{
    protected const PLAIN = 'plain';

    protected const S256  = 'S256';
}
