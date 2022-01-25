<?php

namespace Pinnacle\OpenIdConnect\Authentication\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static StateKey CHALLENGE()
 * @method static StateKey PROVIDER_IDENTIFIER()
 * @method static StateKey PROVIDER_CLIENT_ID()
 * @method static StateKey PROVIDER_CLIENT_SECRET()
 * @method static StateKey PROVIDER_AUTHORIZATION_ENDPOINT()
 * @method static StateKey PROVIDER_TOKEN_ENDPOINT()
 * @method static StateKey PROVIDER_USER_INFO_ENDPOINT()
 * @method static StateKey REDIRECT_URI()
 */
class StateKey extends Enum
{
    protected const CHALLENGE                       = 'challenge';

    protected const PROVIDER_IDENTIFIER             = 'provider.identifier';

    protected const PROVIDER_CLIENT_ID              = 'provider.client-id';

    protected const PROVIDER_CLIENT_SECRET          = 'provider.client-secret';

    protected const PROVIDER_AUTHORIZATION_ENDPOINT = 'provider.authorization-endpoint';

    protected const PROVIDER_TOKEN_ENDPOINT         = 'provider.token-endpoint';

    protected const PROVIDER_USER_INFO_ENDPOINT     = 'provider.user-info-endpoint';

    protected const REDIRECT_URI                    = 'redirect-uri';

    public function withPrefix(string $prefix): string
    {
        return $prefix . '.' . self::getValue();
    }
}
