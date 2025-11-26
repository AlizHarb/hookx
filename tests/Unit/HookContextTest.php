<?php

declare(strict_types=1);

use AlizHarb\Hookx\Context\HookContext;

test('can create hook context', function () {
    $context = new HookContext('test.hook', ['key' => 'value']);
    
    expect($context->getHookName())->toBe('test.hook')
        ->and($context->getArgument('key'))->toBe('value');
});

test('can get all arguments', function () {
    $args = ['name' => 'Alice', 'age' => 30];
    $context = new HookContext('test.hook', $args);
    
    expect($context->getArguments())->toBe($args);
});

test('returns null for non-existent argument', function () {
    $context = new HookContext('test.hook', []);
    
    expect($context->getArgument('nonexistent'))->toBeNull();
});

test('can provide default value for missing argument', function () {
    $context = new HookContext('test.hook', []);
    
    expect($context->getArgument('missing', 'default'))->toBe('default');
});

test('can stop propagation', function () {
    $context = new HookContext('test.hook');
    
    expect($context->isPropagationStopped())->toBeFalse();
    
    $context->stopPropagation();
    
    expect($context->isPropagationStopped())->toBeTrue();
});

test('can set and get data', function () {
    $context = new HookContext('test.hook');
    
    $context->setData('result', 'success');
    
    expect($context->getData('result'))->toBe('success');
});

test('returns null for non-existent data', function () {
    $context = new HookContext('test.hook');
    
    expect($context->getData('nonexistent'))->toBeNull();
});

test('can check if argument exists', function () {
    $context = new HookContext('test.hook', ['exists' => 'value']);
    
    expect($context->hasArgument('exists'))->toBeTrue()
        ->and($context->hasArgument('missing'))->toBeFalse();
});
