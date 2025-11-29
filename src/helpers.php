<?php

declare(strict_types=1);

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Context\HookContext;

if (!function_exists('hook')) {
    /**
     * Dispatch a hook.
     *
     * @param string               $name
     * @param array<string, mixed> $arguments
     * @return HookContext
     */
    function hook(string $name, array $arguments = []): HookContext
    {
        return HookManager::getInstance()->dispatch($name, $arguments);
    }
}

if (!function_exists('filter')) {
    /**
     * Apply filters to a value.
     *
     * @param string               $name
     * @param mixed                $value
     * @param array<string, mixed> $arguments
     * @return mixed
     */
    function filter(string $name, mixed $value, array $arguments = []): mixed
    {
        return HookManager::getInstance()->applyFilters($name, $value, $arguments);
    }
}
