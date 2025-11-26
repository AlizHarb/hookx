<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Sandbox;

use AlizHarb\Hookx\Context\HookContext;
use Throwable;

/**
 * Class Sandbox
 *
 * Provides a safe execution environment for hooks and filters.
 * Catches exceptions to prevent hook failures from crashing the application.
 *
 * @package AlizHarb\Hookx\Sandbox
 */
class Sandbox
{
    /**
     * Execute a hook callback safely.
     *
     * @param callable    $callback The callback to execute.
     * @param HookContext $context  The context to pass to the callback.
     *
     * @return void
     */
    public function execute(callable $callback, HookContext $context): void
    {
        try {
            // Basic error containment
            $callback($context);
        } catch (Throwable $e) {
            // Log error or handle it gracefully without crashing the app
            // In a real sandbox, we might use declare(ticks=1) or pcntl to limit execution time
            error_log("Hook execution failed: " . $e->getMessage());
        }
    }

    /**
     * Execute a filter callback safely.
     *
     * @param callable $callback The callback to execute.
     * @param mixed    ...$args  Arguments to pass to the callback.
     *
     * @return mixed The result of the callback, or the first argument (original value) on failure.
     */
    public function executeSafe(callable $callback, mixed ...$args): mixed
    {
        try {
            return $callback(...$args);
        } catch (Throwable $e) {
            error_log("Filter execution failed: " . $e->getMessage());
            return $args[0] ?? null; // Return original value or null on failure
        }
    }
}
