<?php

namespace Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken;

class Audiences
{
    /**
     * @var Audience[]
     */
    private array $audiences;

    public function __construct(string|array $audiences)
    {
        if (is_string($audiences)) {
            $audiences = [$audiences];
        }

        foreach ($audiences as $audience) {
            $this->audiences[] = new Audience($audience);
        }
    }

    /**
     * @return Audience[]
     */
    public function getAudiences(): array
    {
        return $this->audiences;
    }
}
