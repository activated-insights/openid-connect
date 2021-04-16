<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ResponseType CODE()
 * @method static ResponseType TOKEN()
 */
class ResponseType extends Enum
{
    protected const CODE  = 'code';

    protected const TOKEN = 'token';
}
