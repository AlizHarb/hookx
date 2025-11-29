<?php

use AlizHarb\Hookx\Attributes\Async;
use AlizHarb\Hookx\Attributes\Background;
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Queue\Drivers\SyncDriver;
use AlizHarb\Hookx\Queue\QueueDispatcher;

class AsyncListener
{
    public bool $executed = false;

    #[Hook('async.event')]
    #[Async]
    public function handle(HookContext $context): void
    {
        $this->executed = true;
        \Fiber::suspend();
    }
}

class BackgroundListener
{
    #[Hook('bg.event')]
    #[Background]
    public function handle(HookContext $context): void
    {
        // This won't be executed directly in the test
    }
}

test('async attribute runs in fiber', function () {
    $manager = HookManager::getInstance();
    $manager->reset();

    $listener = new AsyncListener();
    $manager->registerObject($listener);

    $manager->dispatch('async.event');

    expect($listener->executed)->toBeTrue();
});

test('background attribute pushes to queue', function () {
    $manager = HookManager::getInstance();
    $manager->reset();

    // Mock QueueDispatcher
    $driver = new SyncDriver();
    $dispatcher = new class ($driver) extends QueueDispatcher {
        public bool $dispatched = false;
        public function dispatch(string $hookName, array $payload = []): void
        {
            $this->dispatched = true;
        }
    };

    $manager->setQueueDispatcher($dispatcher);

    $listener = new BackgroundListener();
    $manager->registerObject($listener);

    $manager->dispatch('bg.event');

    expect($dispatcher->dispatched)->toBeTrue();
});

test('background attribute throws exception without dispatcher', function () {
    $manager = HookManager::getInstance();
    $manager->reset();

    $listener = new BackgroundListener();

    expect(fn () => $manager->registerObject($listener))
        ->toThrow(RuntimeException::class);
});
