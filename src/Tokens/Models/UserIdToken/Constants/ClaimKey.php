<?php

namespace Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static ClaimKey ISSUER_IDENTIFIER()
 * @method static ClaimKey SUBJECT_IDENTIFIER()
 * @method static ClaimKey AUDIENCES()
 * @method static ClaimKey EXPIRATION_TIME()
 * @method static ClaimKey ISSUED_TIME()
 */
class ClaimKey extends Enum
{
    protected const ISSUER_IDENTIFIER  = 'iss';

    protected const SUBJECT_IDENTIFIER = 'sub';

    protected const AUDIENCES          = 'aud';

    protected const EXPIRATION_TIME    = 'exp';

    protected const ISSUED_TIME        = 'iat';

    /**
     * @return ClaimKey[]
     */
    public static function requiredClaimKeys(): array
    {
        return [
            ClaimKey::ISSUER_IDENTIFIER(),
            ClaimKey::SUBJECT_IDENTIFIER(),
            ClaimKey::AUDIENCES(),
            ClaimKey::EXPIRATION_TIME(),
            ClaimKey::ISSUED_TIME(),
        ];
    }
}
