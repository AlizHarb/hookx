# Laravel Integration

HookX integrates seamlessly with Laravel, providing a powerful, attribute-based event system that complements Laravel's native events.

## Why use HookX with Laravel?

While Laravel has a robust event system, HookX offers:

- **Attribute-based registration**: No need to manually register listeners in `EventServiceProvider`.
- **Dynamic Hooks**: Wildcard (`user.*`) and Regex matching.
- **Filters**: Modify data (like WordPress filters) which Laravel events don't natively support.
- **Priorities**: Fine-grained control over execution order.

---

## 1. Installation

HookX comes with a native **Service Provider** and **Facade** for Laravel.

### Step 1: Register Service Provider

If you are using Laravel 11+, this might be auto-discovered. If not, or if you want to be explicit, add it to `bootstrap/providers.php` or `config/app.php`:

**`config/app.php`**:

```php
'providers' => [
    // ...
    AlizHarb\Hookx\Integrations\Laravel\HookXServiceProvider::class,
],
```

### Step 2: Register Facade (Optional)

If you prefer using the `HookX` facade instead of dependency injection:

**`config/app.php`**:

```php
'aliases' => [
    // ...
    'HookX' => AlizHarb\Hookx\Integrations\Laravel\Facades\HookX::class,
],
```

---

## 2. Usage

### Dispatching Hooks

You can dispatch hooks using the Facade, the helper, or Dependency Injection.

**Using Facade:**

```php
use HookX;

HookX::dispatch('user.registered', ['user' => $user]);
```

**Using Blade Directive:**

```blade
@hook('page.viewed', ['page' => $page])
```

**Using Dependency Injection:**

```php
use AlizHarb\Hookx\HookManager;

public function store(Request $request, HookManager $hooks)
{
    // ...
    $hooks->dispatch('order.created');
}
```

### Applying Filters

Filters allow you to modify data before it's used or rendered.

**In Controller:**

```php
$content = HookX::applyFilters('the_content', $post->content);
```

**In Blade:**

```blade
<div class="content">
    @filter('the_content', $post->content)
</div>
```

---

## 3. Creating Listeners

The best way to handle hooks is by creating dedicated Listener classes.

### Step 1: Create the Class

```bash
php artisan make:class Listeners/UserHooks
```

### Step 2: Add Attributes

Use the `#[Hook]` and `#[Filter]` attributes to register methods.

```php
<?php

namespace App\Listeners;

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\Context\HookContext;
use App\Models\User;

class UserHooks
{
    /**
     * Handle user registration.
     */
    #[Hook('user.registered')]
    public function onRegistered(HookContext $context): void
    {
        $user = $context->getArgument('user');

        // Logic here...
        logger()->info("User registered: {$user->id}");
    }

    /**
     * Filter the user's display name.
     */
    #[Filter('user.name')]
    public function formatName(string $name): string
    {
        return strtoupper($name);
    }
}
```

### Step 3: Register the Listener

You need to tell HookX about your listener class. The best place is in a Service Provider, like `AppServiceProvider`.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use AlizHarb\Hookx\HookManager;
use App\Listeners\UserHooks;

class AppServiceProvider extends ServiceProvider
{
    public function boot(HookManager $hooks): void
    {
        // Register the object so HookX scans its attributes
        $hooks->registerObject(new UserHooks());
    }
}
```

---

## 4. Advanced: Bridging Events

You can automatically dispatch HookX hooks whenever a Laravel event is fired.

```php
// In AppServiceProvider::boot
use Illuminate\Support\Facades\Event;

Event::listen('*', function (string $eventName, array $data) use ($hooks) {
    // Dispatch as a HookX event
    // Note: Laravel wildcard events pass the event name as the first argument
    $hooks->dispatch($eventName, $data);
});
```

---

## 5. Testing

You can mock HookX in your tests easily.

```php
public function test_it_dispatches_hook()
{
    // Mock the facade
    HookX::shouldReceive('dispatch')
        ->once()
        ->with('user.registered', \Mockery::type('array'));

    $this->post('/register', [
        'name' => 'John',
        'email' => 'john@example.com',
        'password' => 'secret',
    ]);
}
```
