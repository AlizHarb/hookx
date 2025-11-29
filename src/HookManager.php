<?php

declare(strict_types=1);

namespace AlizHarb\Hookx;

use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;
use Closure;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class HookManager
 *
 * The central manager for registering and dispatching hooks and filters.
 *
 * @package AlizHarb\Hookx
 */
class HookManager
{
    use Optimization\ZeroCopyDispatch;

    /**
     * The singleton instance.
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Registered hook listeners.
     * Structure: [hookName => [priority => [callbacks]]]
     *
     * @var array<string, array<int, array<callable>>>
     */
    private array $listeners = [];

    /**
     * Registered filters.
     * Structure: [filterName => [priority => [callbacks]]]
     *
     * @var array<string, array<int, array<callable>>>
     */
    private array $filters = [];

    /**
     * Get the singleton instance of the HookManager.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance (for testing purposes).
     *
     * @return void
     */
    public function reset(): void
    {
        $this->listeners = [];
        $this->filters = [];
        $this->strictMode = false;
        $this->logger = null;
        $this->queueDispatcher = null;
    }

    /**
     * Register a callback for a specific hook.
     *
     * @param string   $hookName The name of the hook.
     * @param callable $callback The callback to execute.
     * @param int      $priority The priority of the listener (lower numbers run earlier).
     *
     * @return void
     */
    public function on(string $hookName, callable $callback, int $priority = 10): void
    {
        $this->listeners[$hookName][$priority][] = $callback;
        ksort($this->listeners[$hookName]);
    }

    /**
     * Register a callback for a specific filter.
     *
     * @param string   $filterName The name of the filter.
     * @param callable $callback   The callback to execute.
     * @param int      $priority   The priority of the filter (lower numbers run earlier).
     *
     * @return void
     */
    public function addFilter(string $filterName, callable $callback, int $priority = 10): void
    {
        $this->filters[$filterName][$priority][] = $callback;
        ksort($this->filters[$filterName]);
    }

    /**
     * Strict mode enabled?
     * @var bool
     */
    private bool $strictMode = false;

    /**
     * Enable or disable strict mode.
     *
     * @param bool $enabled
     * @return void
     */
    public function setStrictMode(bool $enabled): void
    {
        $this->strictMode = $enabled;
    }

    /**
     * Logger closure.
     * @var (Closure(string, array<string, mixed>): void)|null
     */
    private ?Closure $logger = null;

    /**
     * Set a logger callback.
     *
     * @param callable $logger
     * @return void
     */
    public function setLogger(callable $logger): void
    {
        $this->logger = $logger(...);
    }

    /**
     * Log a message if a logger is configured.
     *
     * @param string               $message
     * @param array<string, mixed> $context
     * @return void
     */
    private function log(string $message, array $context = []): void
    {
        if ($this->logger) {
            ($this->logger)($message, $context);
        }
    }

    /**
     * Dispatch a hook event.
     *
     * Iterates through all registered listeners for the given hook name,
     * executing them in order of priority. Supports wildcards (*) and regex.
     *
     * @param string               $hookName  The name of the hook to dispatch.
     * @param array<string, mixed> $arguments Optional arguments to pass to the listeners via context.
     *
     * @return HookContext The final context object after all listeners have executed.
     * @throws \RuntimeException If strict mode is enabled and no listeners are found.
     */
    public function dispatch(string $hookName, array $arguments = []): HookContext
    {
        $startTime = microtime(true);
        $this->log("Dispatching hook: {$hookName}", ['arguments' => $arguments]);

        $context = new HookContext($hookName, $arguments);
        $listenersToRun = [];

        // 1. Exact Match
        if (isset($this->listeners[$hookName])) {
            foreach ($this->listeners[$hookName] as $priority => $callbacks) {
                if (! isset($listenersToRun[$priority])) {
                    $listenersToRun[$priority] = [];
                }
                $listenersToRun[$priority] = array_merge($listenersToRun[$priority], $callbacks);
            }
        }

        // 2. Wildcard & Regex Matching
        foreach ($this->listeners as $registeredHook => $priorityGroups) {
            if ($registeredHook === $hookName) {
                continue; // Already handled
            }

            // Wildcard matching (e.g., "user.*" matches "user.registered")
            if (str_contains($registeredHook, '*')) {
                $pattern = '#^' . str_replace('\*', '.*', preg_quote($registeredHook, '#')) . '$#';
                if (preg_match($pattern, $hookName)) {
                    foreach ($priorityGroups as $priority => $callbacks) {
                        if (! isset($listenersToRun[$priority])) {
                            $listenersToRun[$priority] = [];
                        }
                        $listenersToRun[$priority] = array_merge($listenersToRun[$priority], $callbacks);
                    }
                }
            }
            // Regex matching (starts with # or /)
            elseif (str_starts_with($registeredHook, '#') || str_starts_with($registeredHook, '/')) {
                if (preg_match($registeredHook, $hookName)) {
                    foreach ($priorityGroups as $priority => $callbacks) {
                        if (! isset($listenersToRun[$priority])) {
                            $listenersToRun[$priority] = [];
                        }
                        $listenersToRun[$priority] = array_merge($listenersToRun[$priority], $callbacks);
                    }
                }
            }
        }

        if (empty($listenersToRun)) {
            if ($this->strictMode) {
                throw new \RuntimeException("No listeners found for hook: {$hookName}");
            }
            $this->log("No listeners found for hook: {$hookName}");

            return $context;
        }

        // Sort by priority
        ksort($listenersToRun);

        $count = 0;
        foreach ($listenersToRun as $priorityGroup) {
            foreach ($priorityGroup as $callback) {
                if ($context->isPropagationStopped()) {
                    $this->log("Propagation stopped for hook: {$hookName}");

                    break 2;
                }
                $callback($context);
                $count++;
            }
        }

        $duration = microtime(true) - $startTime;
        $this->log("Finished dispatching hook: {$hookName}", ['listeners_executed' => $count, 'duration' => $duration]);

        return $context;
    }

    /**
     * Apply filters to a value.
     *
     * Passes the value through all registered filters for the given filter name,
     * allowing them to modify it.
     *
     * @param string               $filterName The name of the filter.
     * @param mixed                $value      The initial value to filter.
     * @param array<string, mixed> $arguments  Additional arguments to pass to the filter callbacks.
     *
     * @return mixed The filtered value.
     */
    public function applyFilters(string $filterName, mixed $value, array $arguments = []): mixed
    {
        if (! isset($this->filters[$filterName])) {
            return $value;
        }

        foreach ($this->filters[$filterName] as $priorityGroup) {
            foreach ($priorityGroup as $callback) {
                // PHP 8.5 Pipe Operator
                $value = $callback($value, ...$arguments);
            }
        }

        return $value;
    }

    /**
     * @var \AlizHarb\Hookx\Queue\QueueDispatcher|null
     */
    private ?\AlizHarb\Hookx\Queue\QueueDispatcher $queueDispatcher = null;

    /**
     * Set the queue dispatcher for background hooks.
     *
     * @param \AlizHarb\Hookx\Queue\QueueDispatcher $dispatcher
     * @return void
     */
    public function setQueueDispatcher(\AlizHarb\Hookx\Queue\QueueDispatcher $dispatcher): void
    {
        $this->queueDispatcher = $dispatcher;
    }

    /**
     * Register hooks and filters defined in an object using attributes.
     *
     * Scans the object's public methods for #[Hook] and #[Filter] attributes
     * and registers them accordingly.
     *
     * @param object $object The object to scan.
     *
     * @return void
     */
    public function registerObject(object $object): void
    {
        $reflection = new ReflectionClass($object);
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $hookAttributes = $method->getAttributes(Hook::class);
            $backgroundAttr = $method->getAttributes(Attributes\Background::class);
            $asyncAttr = $method->getAttributes(Attributes\Async::class);

            foreach ($hookAttributes as $attribute) {
                $inst = $attribute->newInstance();
                /** @var callable $callback */
                $callback = [$object, $method->getName()];

                // Handle #[Async]
                if (! empty($asyncAttr)) {
                    $originalCallback = $callback;
                    $callback = function (HookContext $context) use ($originalCallback) {
                        $fiber = new \Fiber(function () use ($originalCallback, $context) {
                            $originalCallback($context);
                        });
                        $fiber->start();
                    };
                }

                // Handle #[Background]
                if (! empty($backgroundAttr)) {
                    if (! $this->queueDispatcher) {
                        throw new \RuntimeException("QueueDispatcher not configured but #[Background] attribute used.");
                    }
                    $bgInst = $backgroundAttr[0]->newInstance();
                    $originalCallback = $callback; // Could be the async wrapped one? Usually mutually exclusive.

                    // Note: Serializing the callback/object for queue is complex.
                    // For now, we assume the queue driver handles the payload.
                    // But typically we'd push a job class, not a closure.
                    // Since we can't easily serialize the object method call without more context,
                    // we'll push a generic job payload that the worker needs to know how to handle.
                    // OR: We just execute the dispatch on the queue dispatcher.

                    $callback = function (HookContext $context) use ($inst) {
                        if ($this->queueDispatcher) {
                            $this->queueDispatcher->dispatch($inst->name, $context->getArguments());
                        }
                    };
                }

                $this->on($inst->name, $callback, $inst->priority);
            }

            $filterAttributes = $method->getAttributes(Filter::class);
            foreach ($filterAttributes as $attribute) {
                $inst = $attribute->newInstance();
                /** @var callable $callback */
                $callback = [$object, $method->getName()];
                $this->addFilter($inst->name, $callback, $inst->priority);
            }
        }
    }
}
