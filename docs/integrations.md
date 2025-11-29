# Framework Integrations

HookX is framework-agnostic, but we provide first-class support for the most popular PHP ecosystems.

## Laravel

**Status:** Coming in v1.1.0

The Laravel integration will provide:

- **Service Provider**: Automatically registers the `HookManager` singleton.
- **Facade**: `HookX::dispatch('event')`.
- **Blade Directive**: `@hook('template.header')` to allow plugins to inject HTML into your views.
- **Event Bridge**: Listen to native Laravel events and dispatch them as HookX hooks.

### Manual Setup (Current)

You can easily bind HookX in your `AppServiceProvider`.

```php
// app/Providers/AppServiceProvider.php
use AlizHarb\Hookx\HookManager;

public function register()
{
    $this->app->singleton(HookManager::class, function () {
        return HookManager::getInstance();
    });
}
```

## Symfony

**Status:** Coming in v1.1.0

The Symfony bundle will provide:

- **Dependency Injection**: Autowiring for `HookManager`.
- **Attribute Discovery**: Automatically register services with `#[Hook]` attributes.
- **Profiler**: A debug toolbar panel to see all dispatched hooks and their execution time.

## WordPress

**Status:** Coming in v1.1.0

Modernize your WordPress development by using HookX instead of `add_action` and `add_filter`.

```php
// functions.php
use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Bridge to WP hooks
add_action('init', function () use ($hooks) {
    $hooks->dispatch('wp.init');
});
```
