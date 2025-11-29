<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Integrations\Laravel\Facades;

use AlizHarb\Hookx\HookManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \AlizHarb\Hookx\Context\HookContext dispatch(string $hookName, array<string, mixed> $arguments = [])
 * @method static void on(string $hookName, callable $callback, int $priority = 10)
 * @method static mixed applyFilters(string $filterName, mixed $value, array<string, mixed> $arguments = [])
 * @method static void addFilter(string $filterName, callable $callback, int $priority = 10)
 * @method static void registerObject(object $object)
 *
 * @see \AlizHarb\Hookx\HookManager
 */
class HookX extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HookManager::class;
    }
}
