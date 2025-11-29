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
     * Execute a hook callback safely with optional limits.
     *
     * @param callable    $callback The callback to execute.
     * @param HookContext $context  The context to pass to the callback.
     * @param int         $timeLimitSeconds Maximum execution time in seconds (0 for no limit).
     * @param int         $memoryLimitBytes Maximum memory usage increase in bytes (0 for no limit).
     *
     * @return void
     */
    public function execute(callable $callback, HookContext $context, int $timeLimitSeconds = 0, int $memoryLimitBytes = 0): void
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        try {
            // Basic error containment
            $callback($context);

            // Check limits after execution (soft limit)
            if ($timeLimitSeconds > 0) {
                $duration = microtime(true) - $startTime;
                if ($duration > $timeLimitSeconds) {
                    error_log("Hook execution exceeded time limit: {$duration}s > {$timeLimitSeconds}s");
                }
            }

            if ($memoryLimitBytes > 0) {
                $memoryUsage = memory_get_usage() - $startMemory;
                if ($memoryUsage > $memoryLimitBytes) {
                    error_log("Hook execution exceeded memory limit: {$memoryUsage} bytes > {$memoryLimitBytes} bytes");
                }
            }

        } catch (Throwable $e) {
            // Log error or handle it gracefully without crashing the app
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
