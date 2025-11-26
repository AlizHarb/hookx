# Framework Integrations

HookX is designed to be framework-agnostic while providing first-class support for popular PHP ecosystems.

---

## Supported Frameworks

We provide dedicated integration guides for the following frameworks:

### [Laravel](laravel)

Seamless integration with Laravel's service container and event system.

- **Service Provider** setup
- **Dependency Injection** support
- Bridge for native **Laravel Events**
- **Artisan** commands (coming soon)

### [Symfony](symfony)

Integration with the Symfony ecosystem.

- **Service** registration
- **Dependency Injection** integration
- **EventDispatcher** bridging
- **Profiler** integration (coming soon)

### [WordPress](wordpress)

Modernize your WordPress plugin or theme development.

- Use alongside `add_action` / `add_filter`
- Object-oriented hook management
- Attribute-based registration
- Type-safe hook arguments

---

## Standalone Usage

HookX works perfectly in any PHP 8.3+ application without any framework.

### Basic Setup

```php
use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Register your objects
$hooks->registerObject(new MyListener());

// Dispatch events
$hooks->dispatch('app.ready');
```

For more details on standalone usage, check out the [Basic Usage](basics) guide.

---

## Best Practices

Regardless of which framework you use, follow these general guidelines:

1.  **Dependency Injection**: Always inject `HookManager` where possible instead of using the singleton directly.
2.  **Attributes**: Use PHP 8 attributes (`#[Hook]`, `#[Filter]`) for cleaner, self-documenting code.
3.  **Context**: Leverage the `HookContext` object to pass data and control propagation.
4.  **Type Safety**: Use strict typing in your listener methods to catch errors early.

---

## Next Steps

Select your framework to get started:

- [Laravel Integration](laravel)
- [Symfony Integration](symfony)
- [WordPress Integration](wordpress)
