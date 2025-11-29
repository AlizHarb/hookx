<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Context;

use RuntimeException;

/**
 * Class ImmutableHookContext
 *
 * A read-only version of HookContext.
 * Prevents modification of arguments and data.
 *
 * @package AlizHarb\Hookx\Context
 */
class ImmutableHookContext extends HookContext
{
    /**
     * @param string|int $key
     * @param mixed      $value
     * @return void
     * @throws RuntimeException
     */
    public function setArgument(string|int $key, mixed $value): void
    {
        throw new RuntimeException("Cannot modify arguments in an immutable context.");
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws RuntimeException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException("Cannot modify arguments in an immutable context.");
    }

    /**
     * @param mixed $offset
     * @return void
     * @throws RuntimeException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException("Cannot modify arguments in an immutable context.");
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return void
     * @throws RuntimeException
     */
    public function setData(string $key, mixed $value): void
    {
        throw new RuntimeException("Cannot modify data in an immutable context.");
    }
}
