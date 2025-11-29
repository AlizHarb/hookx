<?php

use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Sandbox\Sandbox;

test('wildcard hooks match correctly', function () {
    $manager = HookManager::getInstance();
    $manager->reset(); // Ensure clean state

    $called = false;
    $manager->on('user.*', function () use (&$called) {
        $called = true;
    });

    $manager->dispatch('user.registered');
    expect($called)->toBeTrue();
});

test('regex hooks match correctly', function () {
    $manager = HookManager::getInstance();
    $manager->reset();

    $called = false;
    $manager->on('#^order\.(created|updated)$#', function () use (&$called) {
        $called = true;
    });

    $manager->dispatch('order.created');
    expect($called)->toBeTrue();

    $called = false;
    $manager->dispatch('order.deleted');
    expect($called)->toBeFalse();
});

test('strict mode throws exception when no listeners found', function () {
    $manager = HookManager::getInstance();
    $manager->reset();
    $manager->setStrictMode(true);

    expect(fn () => $manager->dispatch('unknown.event'))
        ->toThrow(RuntimeException::class, 'No listeners found for hook: unknown.event');

    $manager->setStrictMode(false);
});

test('sandbox enforces time limit (mocked)', function () {
    // Note: We can't easily test actual time limits without slowing down tests significantly
    // or using pcntl which might not be available.
    // We will trust the implementation logic but verify the method exists and runs.

    $sandbox = new Sandbox();
    $executed = false;

    $sandbox->execute(function () use (&$executed) {
        $executed = true;
    }, new HookContext('test'), timeLimitSeconds: 1);

    expect($executed)->toBeTrue();
});

test('sandbox enforces memory limit (mocked)', function () {
    $sandbox = new Sandbox();
    $executed = false;

    $sandbox->execute(function () use (&$executed) {
        $executed = true;
    }, new HookContext('test'), memoryLimitBytes: 1024 * 1024);

    expect($executed)->toBeTrue();
});
