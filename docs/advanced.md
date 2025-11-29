# Advanced Usage

HookX provides a suite of advanced features for power users who need maximum performance, safety, and flexibility.

## Pattern Matching

Beyond exact matches, HookX supports powerful pattern matching for hook names.

### Wildcard Hooks

Use `*` as a wildcard to match any character sequence.

```php
// Matches "user.registered", "user.deleted", etc.
$hooks->on('user.*', function (HookContext $context) {
    echo "User event: " . $context->getHookName();
});
```

### Regex Hooks

For even more control, use regular expressions. Regex hooks must start with `#` or `/`.

```php
// Matches "order.created" or "order.updated" but not "order.deleted"
$hooks->on('#^order\.(created|updated)$#', function (HookContext $context) {
    // ...
});
```

## Strict Mode

By default, dispatching an event with no listeners does nothing. In development, you might want to ensure that every event has at least one listener.

```php
$hooks->setStrictMode(true);

try {
    $hooks->dispatch('typo.event');
} catch (\RuntimeException $e) {
    // "No listeners found for hook: typo.event"
}
```

## Sandbox Limits

To prevent a single hook from crashing your application or consuming too many resources, you can enforce limits in the Sandbox.

```php
use AlizHarb\Hookx\Sandbox\Sandbox;

$sandbox = new Sandbox();

// Execute with 1 second time limit and 10MB memory limit
$sandbox->execute(function ($context) {
    // Heavy operation
}, $context, timeLimitSeconds: 1, memoryLimitBytes: 10 * 1024 * 1024);
```

> **Note:** Time limits use `microtime` checks after execution (soft limit). For hard limits, you would need `pcntl` (not supported in all environments).

## JIT Compilation

**Experimental**

The JIT Compiler optimizes a chain of hooks into a single Closure, removing the overhead of iterating through arrays and checking propagation status on every step.

```php
use AlizHarb\Hookx\Compiler\JITCompiler;

$compiler = new JITCompiler();
$optimizedChain = $compiler->compile($listeners);

// Execute the entire chain as one function
$optimizedChain($context);
```

## Zero-Copy Dispatching

**Experimental**

For extreme performance scenarios where you are passing large arrays and want to avoid any copy-on-write overhead, you can use the `ZeroCopyDispatch` trait.

```php
use AlizHarb\Hookx\Optimization\ZeroCopyDispatch;

class MyDispatcher {
    use ZeroCopyDispatch;
}

$data = ['large' => 'payload'];
$dispatcher->dispatchZeroCopy('event', $data);
```

## Immutable Context

To prevent listeners from modifying the arguments or data passed to them, use `ImmutableHookContext`.

```php
use AlizHarb\Hookx\Context\ImmutableHookContext;

$context = new ImmutableHookContext('event', ['readonly' => true]);

// This will throw a RuntimeException
$context->setArgument('readonly', false);
```
