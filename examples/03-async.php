<?php

require __DIR__ . '/../src/autoload.php';

use AlizHarb\Hookx\Async\AsyncHookDispatcher;
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;

class EmailService
{
    #[Hook('email.send')]
    public function send(HookContext $context): void
    {
        $to = $context->getArgument('to');
        // Simulate delay
        Fiber::suspend();
        echo "Email sent to $to<br>";
    }
}

$manager = HookManager::getInstance();
$manager->registerObject(new EmailService());

$dispatcher = new AsyncHookDispatcher($manager);

echo "<h1>Async Demo</h1>";
echo "Starting async dispatch...<br>";

$dispatcher->dispatchAsync('email.send', ['to' => 'user1@example.com']);
$dispatcher->dispatchAsync('email.send', ['to' => 'user2@example.com']);

echo "Dispatch initiated. (Note: In a real async environment like ReactPHP, this would be non-blocking)<br>";
