# Basic Usage

## Introduction

HookX provides a simple yet powerful way to add extensibility to your PHP applications. This guide covers the fundamental concepts and usage patterns.

## Core Concepts

### 1. Hooks vs Filters

**Hooks** are action points in your code where you want to execute custom logic:

- Don't return values
- Used for side effects (logging, notifications, etc.)
- Multiple listeners can be attached

**Filters** modify and return data:

- Always return a value
- Used for data transformation
- Chain multiple filters together

---

## Getting Started

### Installation

```bash
composer require alizharb/hookx
```

### Basic Hook Example

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Context\HookContext;

// Get the singleton instance
$hooks = HookManager::getInstance();

// Register a listener
$hooks->on('user.registered', function(HookContext $context) {
    $email = $context->getArgument('email');
    $name = $context->getArgument('name');

    // Send welcome email
    mail($email, 'Welcome!', "Hello {$name}, welcome to our platform!");
});

// Dispatch the hook
$hooks->dispatch('user.registered', [
    'email' => 'user@example.com',
    'name' => 'John Doe'
]);
```

**Explanation:**

1. Get the `HookManager` singleton instance
2. Register a listener using `on()` method
3. The listener receives a `HookContext` with all arguments
4. Dispatch the hook with `dispatch()` and pass data

---

## Using Attributes

Attributes provide a cleaner, more declarative way to register hooks.

### Hook Attribute Example

```php
<?php

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;

class UserEventListener
{
    #[Hook('user.registered')]
    public function onUserRegistered(HookContext $context): void
    {
        $user = $context->getArgument('user');

        // Log the registration
        error_log("New user registered: {$user['email']}");
    }

    #[Hook('user.login', priority: 5)]
    public function onUserLogin(HookContext $context): void
    {
        $userId = $context->getArgument('user_id');

        // Update last login timestamp
        // updateLastLogin($userId);
    }
}

// Register the entire object
$manager = HookManager::getInstance();
$manager->registerObject(new UserEventListener());

// Now dispatch hooks
$manager->dispatch('user.registered', [
    'user' => ['email' => 'john@example.com', 'name' => 'John']
]);
```

**Key Points:**

- Use `#[Hook('hook.name')]` attribute on public methods
- Set priority with `priority` parameter (lower = earlier execution)
- Register all hooks at once with `registerObject()`

---

## Priority System

Control the execution order of hooks using priorities.

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Context\HookContext;

$hooks = HookManager::getInstance();

// This runs first (priority 5)
$hooks->on('app.init', function(HookContext $ctx) {
    echo "1. Initialize database\n";
}, priority: 5);

// This runs second (priority 10, default)
$hooks->on('app.init', function(HookContext $ctx) {
    echo "2. Load configuration\n";
}, priority: 10);

// This runs third (priority 20)
$hooks->on('app.init', function(HookContext $ctx) {
    echo "3. Start session\n";
}, priority: 20);

$hooks->dispatch('app.init');

// Output:
// 1. Initialize database
// 2. Load configuration
// 3. Start session
```

**Priority Rules:**

- Lower numbers execute first
- Default priority is 10
- Use priorities to ensure correct execution order

---

## Stop Propagation

Prevent subsequent hooks from executing.

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Context\HookContext;

$hooks = HookManager::getInstance();

$hooks->on('payment.process', function(HookContext $ctx) {
    $amount = $ctx->getArgument('amount');

    if ($amount > 10000) {
        // Stop processing for large amounts
        $ctx->stopPropagation();
        echo "Payment requires manual approval\n";
        return;
    }

    echo "Processing payment of \${$amount}\n";
}, priority: 5);

$hooks->on('payment.process', function(HookContext $ctx) {
    // This won't run if propagation was stopped
    echo "Sending confirmation email\n";
}, priority: 10);

// Test with large amount
$hooks->dispatch('payment.process', ['amount' => 15000]);
// Output: Payment requires manual approval

// Test with normal amount
$hooks->dispatch('payment.process', ['amount' => 100]);
// Output:
// Processing payment of $100
// Sending confirmation email
```

---

## Context Data Storage

Store and retrieve custom data within the hook context.

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Context\HookContext;

$hooks = HookManager::getInstance();

$hooks->on('order.created', function(HookContext $ctx) {
    $orderId = $ctx->getArgument('order_id');

    // Calculate and store discount
    $discount = calculateDiscount($orderId);
    $ctx->setData('discount', $discount);

}, priority: 5);

$hooks->on('order.created', function(HookContext $ctx) {
    // Retrieve the discount calculated earlier
    $discount = $ctx->getData('discount', 0);

    if ($discount > 0) {
        echo "Applied discount: \${$discount}\n";
    }
}, priority: 10);

$context = $hooks->dispatch('order.created', ['order_id' => 123]);

// Access data after dispatch
$finalDiscount = $context->getData('discount');
```

**Use Cases:**

- Pass data between hooks
- Store computed values
- Share state across listeners

---

## Best Practices

### 1. Use Descriptive Hook Names

```php
// ❌ Bad
$hooks->on('user', function($ctx) { ... });

// ✅ Good
$hooks->on('user.registered', function($ctx) { ... });
$hooks->on('user.login.success', function($ctx) { ... });
$hooks->on('user.password.reset', function($ctx) { ... });
```

### 2. Document Your Hooks

```php
/**
 * Fired when a new user registers
 *
 * @hook user.registered
 * @param array $user User data (email, name, id)
 * @param string $source Registration source (web, api, mobile)
 */
$hooks->dispatch('user.registered', [
    'user' => $userData,
    'source' => 'web'
]);
```

### 3. Handle Errors Gracefully

```php
$hooks->on('email.send', function(HookContext $ctx) {
    try {
        $to = $ctx->getArgument('to');
        $subject = $ctx->getArgument('subject');

        // Send email
        sendEmail($to, $subject);

    } catch (\Exception $e) {
        // Log error but don't break other hooks
        error_log("Email send failed: " . $e->getMessage());
    }
});
```

### 4. Keep Hooks Focused

```php
// ❌ Bad - doing too much
$hooks->on('user.registered', function($ctx) {
    sendWelcomeEmail($ctx);
    createUserProfile($ctx);
    assignDefaultRole($ctx);
    logRegistration($ctx);
});

// ✅ Good - separate concerns
$hooks->on('user.registered', function($ctx) {
    sendWelcomeEmail($ctx);
});

$hooks->on('user.registered', function($ctx) {
    createUserProfile($ctx);
});

$hooks->on('user.registered', function($ctx) {
    assignDefaultRole($ctx);
});
```

---

## Common Patterns

### Event Sourcing

```php
class EventStore
{
    #[Hook('*', priority: 1)]
    public function recordEvent(HookContext $ctx): void
    {
        $event = [
            'name' => $ctx->getHookName(),
            'data' => $ctx->getArguments(),
            'timestamp' => time()
        ];

        // Store event
        $this->store($event);
    }
}
```

### Conditional Execution

```php
$hooks->on('cache.clear', function(HookContext $ctx) {
    $type = $ctx->getArgument('type');

    if ($type === 'all' || $type === 'pages') {
        clearPageCache();
    }

    if ($type === 'all' || $type === 'images') {
        clearImageCache();
    }
});
```

### Plugin Architecture

```php
interface Plugin
{
    public function register(HookManager $hooks): void;
}

class AnalyticsPlugin implements Plugin
{
    public function register(HookManager $hooks): void
    {
        $hooks->on('page.view', [$this, 'trackPageView']);
        $hooks->on('user.action', [$this, 'trackAction']);
    }

    public function trackPageView(HookContext $ctx): void
    {
        // Track page view
    }

    public function trackAction(HookContext $ctx): void
    {
        // Track user action
    }
}

// Load plugins
$plugin = new AnalyticsPlugin();
$plugin->register($hooks);
```

---

## Next Steps

- Learn about [Filters](filters.md) for data transformation
- Explore [Async Hooks](async.md) for non-blocking operations
- See [Framework Integrations](integrations.md) for Laravel, Symfony, etc.
