# Basic Usage

This guide covers the fundamental concepts of using HookX: registering listeners, dispatching events, and managing control flow.

## The Hook Manager

The `HookManager` is the central hub of HookX. It is responsible for:

1.  Storing registered listeners.
2.  Dispatching events.
3.  Managing the execution flow.

You can access it via the singleton instance or instantiate it directly if you are using a dependency injection container.

```php
use AlizHarb\Hookx\HookManager;

// Singleton access
$hooks = HookManager::getInstance();

// Or direct instantiation
$hooks = new HookManager();
```

## Defining Listeners

The most modern and recommended way to define listeners is using **Attributes**. This keeps your configuration close to your code.

### Using `#[Hook]`

To listen for an event, add the `#[Hook]` attribute to a public method.

```php
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserSubscriber
{
    #[Hook('user.registered')]
    public function onUserRegistered(HookContext $context): void
    {
        $email = $context->getArgument('email');
        // Send welcome email...
    }
}
```

### Registering Objects

Once you have defined your listener class, you must register it with the manager.

```php
$subscriber = new UserSubscriber();
$hooks->registerObject($subscriber);
```

The `registerObject` method scans the object for any methods with `#[Hook]` or `#[Filter]` attributes and registers them automatically.

## Dispatching Events

To trigger an event, use the `dispatch` method.

```php
$hooks->dispatch('user.registered', [
    'email' => 'alice@example.com',
    'name' => 'Alice'
]);
```

### The `HookContext`

Every listener receives a `HookContext` object. This object provides access to the arguments passed during dispatch and allows you to control the flow.

```php
public function onEvent(HookContext $context): void
{
    // Get all arguments
    $args = $context->getArguments();

    // Get a specific argument
    $userId = $context->getArgument('user_id');

    // Stop other listeners from running
    $context->stopPropagation();
}
```

## Priorities

You can control the order in which listeners execute using the `priority` argument. Lower numbers run first. The default priority is `10`.

We provide a `Priority` class with useful constants:

```php
use AlizHarb\Hookx\Priority;

#[Hook('order.created', priority: Priority::HIGH)] // 5
public function highPriority(HookContext $context): void
{
    // Runs first
}

#[Hook('order.created', priority: Priority::LOW)] // 20
public function lowPriority(HookContext $context): void
{
    // Runs later
}
```

## Manual Registration

If you prefer not to use attributes, you can register callbacks manually using the `on` method.

```php
$hooks->on('user.login', function (HookContext $context) {
    // Handle login
}, priority: 10);
```

## Helper Functions (v1.1.0)

HookX provides global helper functions for convenience.

### `hook()`

Dispatches a hook.

```php
// Equivalent to HookManager::getInstance()->dispatch(...)
hook('user.registered', ['id' => 1]);
```

### `filter()`

Applies filters.

```php
// Equivalent to HookManager::getInstance()->applyFilters(...)
$title = filter('page.title', 'Home');
```
