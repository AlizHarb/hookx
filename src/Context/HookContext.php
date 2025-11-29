<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Context;

use ArrayAccess;

/**
 * Class HookContext
 *
 * Represents the context passed to hook listeners.
 * Allows passing data between listeners and controlling propagation.
 *
 * @package AlizHarb\Hookx\Context
 * @implements ArrayAccess<string, mixed>
 */
class HookContext implements ArrayAccess
{
    /**
     * Whether the hook propagation has been stopped.
     * @var bool
     */
    private bool $propagationStopped = false;

    /**
     * @param string                   $hookName  The name of the hook being executed.
     * @param array<string|int, mixed> $arguments Initial arguments passed to the hook.
     */
    public function __construct(
        public readonly string $hookName,
        private array $arguments = [],
    ) {}

    /**
     * Create a new context with modified arguments using PHP 8.5 clone with.
     *
     * @param array $newArguments
     * @return self
     */
    public function with(array $newArguments): self
    {
        return new self(
            $this->hookName,
            array_merge($this->arguments, $newArguments)
        );
    }

    /**
     * Stop the propagation of the hook.
     * Subsequent listeners will not be executed.
     *
     * @return void
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Check if propagation has been stopped.
     *
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Get all arguments.
     *
     * @return array<string|int, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get a specific argument by key.
     *
     * @param string|int $key     The argument key.
     * @param mixed      $default Default value if key does not exist.
     *
     * @return mixed
     */
    public function getArgument(string|int $key, mixed $default = null): mixed
    {
        return $this->arguments[$key] ?? $default;
    }

    /**
     * Set an argument value.
     *
     * @param string|int $key   The argument key.
     * @param mixed      $value The value to set.
     *
     * @return void
     */
    public function setArgument(string|int $key, mixed $value): void
    {
        $this->arguments[$key] = $value;
    }

    /**
     * Check if an offset exists (ArrayAccess).
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        /** @var string|int $offset */
        return isset($this->arguments[$offset]);
    }

    /**
     * Get an offset value (ArrayAccess).
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        /** @var string|int $offset */
        return $this->arguments[$offset] ?? null;
    }

    /**
     * Set an offset value (ArrayAccess).
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->arguments[] = $value;
        } else {
            /** @var string|int $offset */
            $this->arguments[$offset] = $value;
        }
    }

    /**
     * Unset an offset (ArrayAccess).
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        unset($this->arguments[$offset]);
    }

    /**
     * Get the name of the current hook.
     *
     * @return string
     */
    public function getHookName(): string
    {
        return $this->hookName;
    }

    /**
     * Check if a specific argument exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasArgument(string $key): bool
    {
        return array_key_exists($key, $this->arguments);
    }

    /**
     * Additional arbitrary data storage.
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Set arbitrary data in the context.
     * Useful for passing data between listeners that isn't part of the main arguments.
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function setData(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get arbitrary data from the context.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
