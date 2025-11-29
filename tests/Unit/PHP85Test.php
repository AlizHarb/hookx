<?php

use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;

test('clone with syntax works in HookContext', function () {
    $context = new HookContext('test.hook', ['foo' => 'bar']);

    // This uses the new with() method which uses `clone $this with { ... }`
    $newContext = $context->with(['baz' => 'qux']);

    expect($context)->not->toBe($newContext)
        ->and($context->getArgument('foo'))->toBe('bar')
        ->and($context->getArgument('baz'))->toBeNull()
        ->and($newContext->getArgument('foo'))->toBe('bar')
        ->and($newContext->getArgument('baz'))->toBe('qux');
});

test('pipe operator works in applyFilters', function () {
    $manager = HookManager::getInstance();
    $manager->reset();

    $manager->addFilter('test.pipe', function ($value) {
        return $value . ' | first';
    });

    $manager->addFilter('test.pipe', function ($value) {
        return $value . ' | second';
    }, 20);

    // The implementation uses $value |> $callback($$, ...$args)
    $result = $manager->applyFilters('test.pipe', 'start');

    expect($result)->toBe('start | first | second');
});

test('helpers work', function () {
    $manager = HookManager::getInstance();
    $manager->reset();

    $called = false;
    $manager->on('helper.test', function () use (&$called) {
        $called = true;
    });

    hook('helper.test');

    expect($called)->toBeTrue();

    $manager->addFilter('helper.filter', fn ($val) => strtoupper($val));

    expect(filter('helper.filter', 'hello'))->toBe('HELLO');
});
