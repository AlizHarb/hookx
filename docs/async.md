# Async & Background Hooks

HookX provides two powerful ways to handle tasks that shouldn't block your main execution flow: **Async Hooks** (using Fibers) and **Background Hooks** (using Queues).

## Async Hooks (Fibers)

Async hooks run concurrently within the same PHP process. They are useful for I/O bound tasks where you want to initiate multiple operations (like API calls) and wait for them all to complete, or simply fire-and-forget within the lifespan of the request.

### Usage

Use the `AsyncHookDispatcher` to dispatch events asynchronously.

```php
use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\HookManager;

$manager = HookManager::getInstance();
$asyncDispatcher = new AsyncHookDispatcher($manager);

// Dispatch and continue immediately
$asyncDispatcher->dispatchAsync('email.send', ['to' => 'user@example.com']);

echo "Email is sending in background...";
```

### Concurrent Dispatch

You can also dispatch multiple events and let them run in parallel (cooperatively).

```php
$asyncDispatcher->dispatchConcurrent([
    'analytics.track' => ['event' => 'page_view'],
    'cache.warm' => ['key' => 'home_page'],
]);
```

> **Note:** Since PHP is single-threaded, Fibers are cooperative. CPU-intensive tasks will still block the main thread. Async hooks are best for I/O operations.

---

## Background Hooks (Queues)

**New in v1.1.0**

Background hooks are fully offloaded to a queue system (like Redis), allowing your main application to respond immediately while a worker process handles the heavy lifting. This is ideal for:

- Sending emails
- Processing image uploads
- Generating reports
- Webhook delivery

### Setup

First, configure the `QueueDispatcher` with a driver.

```php
use AlizHarb\Hookx\Queue\QueueDispatcher;
use AlizHarb\Hookx\Queue\Drivers\RedisDriver;

// Setup Redis connection
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

// Initialize Driver and Dispatcher
$driver = new RedisDriver($redis, 'hookx_queue');
$queueDispatcher = new QueueDispatcher($driver);
```

### Dispatching to Queue

Simply use the `dispatch` method on the queue dispatcher.

```php
$queueDispatcher->dispatch('report.generate', [
    'report_id' => 123,
    'type' => 'pdf'
]);
```

The job is now pushed to Redis. You will need a worker script to process these jobs.

### Processing the Queue

You need a worker script that pops jobs from the queue and executes them.

```php
// worker.php
while (true) {
    $job = $redis->blPop('hookx_queue', 10);
    if ($job) {
        $data = json_decode($job[1], true);

        // Re-dispatch locally in the worker
        $manager->dispatch($data['job'], $data['payload']);
    }
}
```

## Choosing the Right Tool

| Feature         | Async (Fibers)                           | Background (Queue)                           |
| :-------------- | :--------------------------------------- | :------------------------------------------- |
| **Execution**   | Concurrent (Same Process)                | Parallel (Worker Process)                    |
| **Latency**     | Low                                      | Medium (Queue overhead)                      |
| **Reliability** | Tied to request lifecycle                | High (Retryable)                             |
| **Use Case**    | Parallel API calls, non-critical updates | Emails, heavy computation, reliable delivery |

## Using Attributes

HookX v1.1.0 introduces `#[Async]` and `#[Background]` attributes for declarative configuration.

### Async Attribute

Mark a listener as async to automatically run it in a Fiber.

```php
use AlizHarb\Hookx\Attributes\{Hook, Async};

class PaymentListener
{
    #[Hook('payment.processed')]
    #[Async]
    public function onPayment(HookContext $context): void
    {
        // This runs in a Fiber
        $this->notifyExternalService();
    }
}
```

### Background Attribute

Mark a listener to be executed in the background via the configured queue.

```php
use AlizHarb\Hookx\Attributes\{Hook, Background};

class EmailListener
{
    #[Hook('user.registered')]
    #[Background] // Pushes to queue automatically
    public function sendWelcomeEmail(HookContext $context): void
    {
        // This logic runs in the background worker
    }
}
```

> **Note:** For `#[Background]` to work, you must configure the `QueueDispatcher` on the `HookManager`.

```php
$hooks->setQueueDispatcher($dispatcher);
```
