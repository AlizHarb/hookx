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
     * Dispatch a hook event.
     *
     * Iterates through all registered listeners for the given hook name,
     * executing them in order of priority.
     *
     * @param string               $hookName  The name of the hook to dispatch.
     * @param array<string, mixed> $arguments Optional arguments to pass to the listeners via context.
     *
     * @return HookContext The final context object after all listeners have executed.
     */
    public function dispatch(string $hookName, array $arguments = []): HookContext
    {
        $context = new HookContext($hookName, $arguments);

        if (!isset($this->listeners[$hookName])) {
            return $context;
        }

        foreach ($this->listeners[$hookName] as $priorityGroup) {
            foreach ($priorityGroup as $callback) {
                if ($context->isPropagationStopped()) {
                    break 2;
                }
                $callback($context);
            }
        }

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
        if (!isset($this->filters[$filterName])) {
            return $value;
        }

        foreach ($this->filters[$filterName] as $priorityGroup) {
            foreach ($priorityGroup as $callback) {
                $value = $callback($value, ...$arguments);
            }
        }

        return $value;
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
            foreach ($hookAttributes as $attribute) {
                $inst = $attribute->newInstance();
                /** @var callable $callback */
                $callback = [$object, $method->getName()];
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
