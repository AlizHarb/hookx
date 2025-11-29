<?php

declare(strict_types=1);

use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;

beforeEach(function () {
    $this->manager = freshHookManager();
});

test('can get singleton instance', function () {
    $manager1 = HookManager::getInstance();
    $manager2 = HookManager::getInstance();

    expect($manager1)->toBe($manager2);
});

test('can register hook listener', function () {
    $called = false;

    $this->manager->on('test.hook', function (HookContext $context) use (&$called) {
        $called = true;
    });

    $this->manager->dispatch('test.hook');

    expect($called)->toBeTrue();
});

test('can pass arguments to hook', function () {
    $receivedName = null;

    $this->manager->on('user.created', function (HookContext $context) use (&$receivedName) {
        $receivedName = $context->getArgument('name');
    });

    $this->manager->dispatch('user.created', ['name' => 'Alice']);

    expect($receivedName)->toBe('Alice');
});

test('respects hook priority', function () {
    $order = [];

    $this->manager->on('test.priority', function () use (&$order) {
        $order[] = 'second';
    }, 20);

    $this->manager->on('test.priority', function () use (&$order) {
        $order[] = 'first';
    }, 10);

    $this->manager->on('test.priority', function () use (&$order) {
        $order[] = 'third';
    }, 30);

    $this->manager->dispatch('test.priority');

    expect($order)->toBe(['first', 'second', 'third']);
});

test('can stop propagation', function () {
    $firstCalled = false;
    $secondCalled = false;

    $this->manager->on('test.stop', function (HookContext $context) use (&$firstCalled) {
        $firstCalled = true;
        $context->stopPropagation();
    }, 10);

    $this->manager->on('test.stop', function () use (&$secondCalled) {
        $secondCalled = true;
    }, 20);

    $this->manager->dispatch('test.stop');

    expect($firstCalled)->toBeTrue()
        ->and($secondCalled)->toBeFalse();
});

test('returns hook context from dispatch', function () {
    $context = $this->manager->dispatch('test.context', ['key' => 'value']);

    expect($context)->toBeInstanceOf(HookContext::class)
        ->and($context->getHookName())->toBe('test.context')
        ->and($context->getArgument('key'))->toBe('value');
});

test('handles non-existent hooks gracefully', function () {
    $context = $this->manager->dispatch('non.existent');

    expect($context)->toBeInstanceOf(HookContext::class);
});

test('can register multiple listeners for same hook', function () {
    $count = 0;

    $this->manager->on('test.multiple', function () use (&$count) {
        $count++;
    });

    $this->manager->on('test.multiple', function () use (&$count) {
        $count++;
    });

    $this->manager->dispatch('test.multiple');

    expect($count)->toBe(2);
});
