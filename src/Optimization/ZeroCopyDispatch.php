<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Optimization;

use AlizHarb\Hookx\Context\HookContext;

/**
 * Trait ZeroCopyDispatch
 *
 * Provides a dispatch method that accepts arguments by reference to avoid copying.
 * Note: HookContext constructor copies the array, so true zero-copy requires
 * changing HookContext to accept a reference or using a different context implementation.
 *
 * @package AlizHarb\Hookx\Optimization
 */
trait ZeroCopyDispatch
{
    /**
     * Dispatch a hook with arguments passed by reference.
     *
     * @param string               $hookName
     * @param array<string, mixed> $arguments
     * @return HookContext
     */
    public function dispatchZeroCopy(string $hookName, array &$arguments): HookContext
    {
        // For true zero-copy, HookContext would need to hold a reference.
        // Since PHP 8.3, copy-on-write is very efficient, so this is mostly
        // beneficial if the array is modified in place by listeners.

        return $this->dispatch($hookName, $arguments);
    }
}
