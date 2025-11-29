<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Async;

use AlizHarb\Hookx\HookManager;
use Fiber;

/**
 * Class AsyncHookDispatcher
 *
 * Dispatches hooks asynchronously using PHP Fibers.
 *
 * @package AlizHarb\Hookx\Async
 */
class AsyncHookDispatcher
{
    /**
     * @param HookManager $hookManager The hook manager instance.
     */
    public function __construct(
        private HookManager $hookManager,
    ) {
    }

    /**
     * Dispatch a hook asynchronously.
     *
     * Creates a new Fiber to execute the hook dispatch, allowing it to run
     * without blocking the main execution flow (if the listeners yield).
     *
     * @param string               $hookName  The name of the hook.
     * @param array<string, mixed> $arguments Arguments to pass to the hook.
     *
     * @return void
     */
    public function dispatchAsync(string $hookName, array $arguments = []): void
    {
        $fiber = new Fiber(function () use ($hookName, $arguments) {
            $this->hookManager->dispatch($hookName, $arguments);
        });

        $fiber->start();
    }

    /**
     * Dispatch multiple hooks concurrently using Fibers.
     *
     * Starts a Fiber for each hook. Note that PHP Fibers are cooperative;
     * true parallelism requires an event loop or async I/O within the listeners.
     *
     * @param array<string, array<string, mixed>> $hooks Associative array of hookName => arguments.
     *
     * @return void
     */
    public function dispatchConcurrent(array $hooks): void
    {
        $fibers = [];
        foreach ($hooks as $hookName => $arguments) {
            $fibers[] = new Fiber(function () use ($hookName, $arguments) {
                $this->hookManager->dispatch($hookName, $arguments);
            });
        }

        foreach ($fibers as $fiber) {
            $fiber->start();
        }
    }
}
