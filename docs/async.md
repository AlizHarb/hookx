# Async Hooks

## Introduction

HookX supports asynchronous hook execution using PHP Fibers, allowing you to run time-consuming operations without blocking your main application flow.

## Why Async Hooks?

Async hooks are perfect for:

- **Email sending** - Don't wait for SMTP responses
- **API calls** - External HTTP requests
- **Image processing** - Resize, optimize, watermark
- **Logging** - Write to external services
- **Analytics** - Track events without slowing down requests

---

## Basic Async Usage

### Simple Async Dispatch

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\Context\HookContext;

// Get hook manager
$manager = HookManager::getInstance();

// Register a slow hook
$manager->on('email.send', function(HookContext $ctx) {
    $to = $ctx->getArgument('to');
    $subject = $ctx->getArgument('subject');

    // This might take 2-3 seconds
    mail($to, $subject, $ctx->getArgument('body'));

    echo "Email sent to {$to}\n";
});

// Create async dispatcher
$async = new AsyncHookDispatcher($manager);

// Dispatch asynchronously - returns immediately!
echo "Starting email send...\n";
$async->dispatchAsync('email.send', [
    'to' => 'user@example.com',
    'subject' => 'Welcome!',
    'body' => 'Thanks for signing up!'
]);
echo "Email queued! Continuing with other work...\n";

// Do other work while email sends in background
processUserData();
updateDatabase();

// Email will be sent in the background
```

**Output:**

```
Starting email send...
Email queued! Continuing with other work...
[... other work happens ...]
Email sent to user@example.com
```

---

## Real-World Examples

### 1. User Registration with Async Notifications

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserNotifications
{
    #[Hook('user.registered')]
    public function sendWelcomeEmail(HookContext $ctx): void
    {
        $user = $ctx->getArgument('user');

        // Simulate slow email sending (2 seconds)
        sleep(2);

        echo "✉️  Welcome email sent to {$user['email']}\n";
    }

    #[Hook('user.registered')]
    public function notifyAdmins(HookContext $ctx): void
    {
        $user = $ctx->getArgument('user');

        // Simulate API call to Slack (1 second)
        sleep(1);

        echo "📢 Admin notification sent for {$user['name']}\n";
    }

    #[Hook('user.registered')]
    public function createAnalyticsProfile(HookContext $ctx): void
    {
        $user = $ctx->getArgument('user');

        // Simulate analytics API call (1.5 seconds)
        sleep(1.5);

        echo "📊 Analytics profile created for user #{$user['id']}\n";
    }
}

// Setup
$manager = HookManager::getInstance();
$manager->registerObject(new UserNotifications());
$async = new AsyncHookDispatcher($manager);

// Register user
$user = [
    'id' => 123,
    'name' => 'John Doe',
    'email' => 'john@example.com'
];

echo "🚀 Registering user...\n";
$start = microtime(true);

// Dispatch async - all notifications run in parallel!
$async->dispatchAsync('user.registered', ['user' => $user]);

$elapsed = microtime(true) - $start;
echo "✅ User registered in " . round($elapsed, 2) . " seconds!\n";
echo "👤 User can now log in while notifications are being sent...\n";

// Without async: would take 4.5 seconds (2 + 1 + 1.5)
// With async: takes ~0.01 seconds, notifications happen in background
```

---

### 2. Image Processing Pipeline

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class ImageProcessor
{
    #[Hook('image.uploaded')]
    public function createThumbnail(HookContext $ctx): void
    {
        $imagePath = $ctx->getArgument('path');

        echo "📸 Creating thumbnail for {$imagePath}...\n";

        // Simulate image processing
        sleep(2);

        echo "✅ Thumbnail created\n";
    }

    #[Hook('image.uploaded')]
    public function optimizeImage(HookContext $ctx): void
    {
        $imagePath = $ctx->getArgument('path');

        echo "🗜️  Optimizing {$imagePath}...\n";

        // Simulate optimization
        sleep(3);

        echo "✅ Image optimized\n";
    }

    #[Hook('image.uploaded')]
    public function uploadToCDN(HookContext $ctx): void
    {
        $imagePath = $ctx->getArgument('path');

        echo "☁️  Uploading to CDN...\n";

        // Simulate CDN upload
        sleep(2);

        echo "✅ Uploaded to CDN\n";
    }
}

// Setup
$manager = HookManager::getInstance();
$manager->registerObject(new ImageProcessor());
$async = new AsyncHookDispatcher($manager);

// User uploads image
echo "📤 User uploading image...\n";
$start = microtime(true);

$async->dispatchAsync('image.uploaded', [
    'path' => '/uploads/photo.jpg',
    'user_id' => 456
]);

$elapsed = microtime(true) - $start;
echo "✅ Upload complete in " . round($elapsed, 2) . " seconds!\n";
echo "👤 User can continue browsing while processing happens...\n";

// Processing happens in background:
// - Thumbnail creation (2s)
// - Optimization (3s)
// - CDN upload (2s)
// All run in parallel!
```

---

### 3. API Integration with Error Handling

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\Context\HookContext;

$manager = HookManager::getInstance();

// Hook with error handling
$manager->on('order.created', function(HookContext $ctx) {
    $orderId = $ctx->getArgument('order_id');

    try {
        echo "📦 Syncing order #{$orderId} to warehouse API...\n";

        // Simulate API call
        sleep(2);

        // Simulate occasional failure
        if (rand(1, 10) > 8) {
            throw new \Exception("API timeout");
        }

        echo "✅ Order synced successfully\n";

    } catch (\Exception $e) {
        // Log error but don't crash
        error_log("Failed to sync order #{$orderId}: " . $e->getMessage());
        echo "❌ Sync failed, will retry later\n";

        // Could queue for retry
        queueForRetry($orderId);
    }
});

$async = new AsyncHookDispatcher($manager);

// Create order
echo "🛒 Creating order...\n";
$async->dispatchAsync('order.created', ['order_id' => 789]);
echo "✅ Order created! Syncing in background...\n";
```

---

## Concurrent Execution

Execute multiple different hooks concurrently.

```php
<?php

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\Context\HookContext;

$manager = HookManager::getInstance();

// Register various hooks
$manager->on('cache.warm', function(HookContext $ctx) {
    echo "🔥 Warming cache...\n";
    sleep(3);
    echo "✅ Cache warmed\n";
});

$manager->on('sitemap.generate', function(HookContext $ctx) {
    echo "🗺️  Generating sitemap...\n";
    sleep(2);
    echo "✅ Sitemap generated\n";
});

$manager->on('backup.create', function(HookContext $ctx) {
    echo "💾 Creating backup...\n";
    sleep(4);
    echo "✅ Backup created\n";
});

$async = new AsyncHookDispatcher($manager);

// Run all maintenance tasks concurrently
echo "🔧 Starting maintenance tasks...\n";
$start = microtime(true);

$async->dispatchConcurrent([
    ['cache.warm', []],
    ['sitemap.generate', []],
    ['backup.create', []]
]);

$elapsed = microtime(true) - $start;
echo "✅ All tasks queued in " . round($elapsed, 2) . " seconds!\n";

// Without async: 3 + 2 + 4 = 9 seconds
// With async: ~0.01 seconds, tasks run in parallel
```

---

## Best Practices

### 1. Use Async for I/O Operations

```php
// ✅ Good - I/O bound operations
$async->dispatchAsync('email.send', $data);        // Network I/O
$async->dispatchAsync('file.process', $data);      // Disk I/O
$async->dispatchAsync('api.call', $data);          // Network I/O
$async->dispatchAsync('database.backup', $data);   // Disk I/O

// ❌ Bad - CPU bound operations (use queues instead)
$async->dispatchAsync('video.encode', $data);      // CPU intensive
$async->dispatchAsync('data.crunch', $data);       // CPU intensive
```

### 2. Handle Errors Gracefully

```php
$manager->on('async.task', function(HookContext $ctx) {
    try {
        // Risky operation
        performRiskyOperation();
    } catch (\Exception $e) {
        // Log and handle
        error_log($e->getMessage());

        // Store for retry
        saveFailedTask($ctx->getArguments());
    }
});
```

### 3. Don't Rely on Return Values

```php
// ❌ Bad - async hooks don't return values
$result = $async->dispatchAsync('calculate.total', $data);
// $result is NOT the calculation result!

// ✅ Good - use callbacks or events
$manager->on('calculate.total', function(HookContext $ctx) {
    $total = calculateTotal($ctx->getArgument('items'));

    // Store result or dispatch another event
    $ctx->setData('total', $total);
    $manager->dispatch('calculation.complete', ['total' => $total]);
});
```

### 4. Monitor Background Tasks

```php
class TaskMonitor
{
    private array $tasks = [];

    #[Hook('*', priority: 1)]
    public function trackTask(HookContext $ctx): void
    {
        $taskId = uniqid();

        $this->tasks[$taskId] = [
            'hook' => $ctx->getHookName(),
            'started' => microtime(true),
            'status' => 'running'
        ];

        // Log task
        echo "📝 Task {$taskId}: {$ctx->getHookName()} started\n";
    }
}
```

---

## Performance Considerations

### Memory Usage

```php
// Async hooks use Fibers which have memory overhead
// For many concurrent tasks, consider:

// 1. Batch processing
$batches = array_chunk($items, 10);
foreach ($batches as $batch) {
    foreach ($batch as $item) {
        $async->dispatchAsync('process.item', ['item' => $item]);
    }
    // Wait for batch to complete before next batch
    usleep(100000); // 100ms
}

// 2. Use a proper queue system for large-scale async work
// (Laravel Queues, RabbitMQ, etc.)
```

### Timeouts

```php
// Set reasonable timeouts for async operations
$manager->on('external.api', function(HookContext $ctx) {
    $timeout = 5; // 5 seconds
    $start = time();

    while (time() - $start < $timeout) {
        try {
            $result = callExternalAPI();
            return;
        } catch (\Exception $e) {
            if (time() - $start >= $timeout) {
                error_log("API call timed out after {$timeout}s");
                return;
            }
            usleep(500000); // Wait 500ms before retry
        }
    }
});
```

---

## Debugging Async Hooks

### Logging

```php
$manager->on('debug.log', function(HookContext $ctx) {
    $message = sprintf(
        "[%s] %s: %s\n",
        date('Y-m-d H:i:s'),
        $ctx->getHookName(),
        json_encode($ctx->getArguments())
    );

    file_put_contents('async-hooks.log', $message, FILE_APPEND);
});

// Log all async dispatches
$async->dispatchAsync('debug.log', [
    'action' => 'email.send',
    'to' => 'user@example.com'
]);
```

### Profiling

```php
class AsyncProfiler
{
    private array $timings = [];

    public function start(string $hookName): void
    {
        $this->timings[$hookName] = microtime(true);
    }

    public function end(string $hookName): void
    {
        if (isset($this->timings[$hookName])) {
            $elapsed = microtime(true) - $this->timings[$hookName];
            echo "⏱️  {$hookName} took " . round($elapsed, 3) . "s\n";
        }
    }
}
```

---

## When NOT to Use Async

- **Database transactions** - Keep synchronous for consistency
- **Critical path operations** - User must wait for these
- **Operations requiring immediate feedback** - Use sync hooks
- **Very short operations** - Async overhead not worth it (< 100ms)

```php
// ❌ Don't use async for critical operations
$async->dispatchAsync('payment.process', $data);  // BAD!

// ✅ Use sync for critical operations
$manager->dispatch('payment.process', $data);     // GOOD!

// ✅ Use async for follow-up actions
$async->dispatchAsync('payment.receipt.send', $data);  // GOOD!
```

---

## Next Steps

- Learn about [Filters](filters.md) for data transformation
- See [Framework Integrations](integrations.md) for Laravel, Symfony, etc.
- Explore [Basic Hooks](basics.md) for synchronous hooks
