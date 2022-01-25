<?php

namespace Pinnacle\OpenIdConnect\Authentication\StatePersister\Contracts;

/**
 * Interface used to define how state values should be stored and retrieved within an application.
 */
interface StatePersisterInterface
{
    /**
     * Handles retrieving a value from storage with the provided key.
     */
    public function getValue(string $key): mixed;

    /**
     * Handles persisting the provided value using the provided key as reference to retrieve the value later.
     */
    public function storeValue(string $key, mixed $value): void;
}
