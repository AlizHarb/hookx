<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Compiler;

use AlizHarb\Hookx\Context\HookContext;
use Closure;

/**
 * Class JITCompiler
 *
 * Compiles a chain of hook callbacks into a single optimized Closure.
 * This reduces the overhead of iterating through arrays and checking propagation
 * status on every step during dispatch.
 *
 * @package AlizHarb\Hookx\Compiler
 */
class JITCompiler
{
    /**
     * Compile a list of callbacks into a single Closure.
     *
     * @param array<callable> $callbacks
     * @return Closure(HookContext): void
     */
    public function compile(array $callbacks): Closure
    {
        if (empty($callbacks)) {
            return function (HookContext $context) {};
        }

        // Reverse the array to build the chain from the inside out
        $chain = null;

        foreach (array_reverse($callbacks) as $callback) {
            $next = $chain;
            $chain = function (HookContext $context) use ($callback, $next) {
                if ($context->isPropagationStopped()) {
                    return;
                }

                $callback($context);

                if ($next) {
                    $next($context);
                }
            };
        }

        return $chain;
    }
}
