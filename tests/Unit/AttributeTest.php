<?php

declare(strict_types=1);

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\Context\HookContext;

beforeEach(function () {
    $this->manager = freshHookManager();
});

test('can register hooks using attributes', function () {
    $listener = new class {
        public bool $called = false;
        
        #[Hook('test.attribute')]
        public function onTest(HookContext $context): void
        {
            $this->called = true;
        }
    };
    
    $this->manager->registerObject($listener);
    $this->manager->dispatch('test.attribute');
    
    expect($listener->called)->toBeTrue();
});

test('can register filters using attributes', function () {
    $listener = new class {
        #[Filter('text.uppercase')]
        public function makeUppercase(string $text): string
        {
            return strtoupper($text);
        }
    };
    
    $this->manager->registerObject($listener);
    $result = $this->manager->applyFilters('text.uppercase', 'hello');
    
    expect($result)->toBe('HELLO');
});

test('respects priority in attributes', function () {
    $order = [];
    
    $listener = new class {
        public array $order = [];
        
        #[Hook('test.priority', priority: 20)]
        public function second(HookContext $context): void
        {
            $this->order[] = 'second';
        }
        
        #[Hook('test.priority', priority: 10)]
        public function first(HookContext $context): void
        {
            $this->order[] = 'first';
        }
    };
    
    $this->manager->registerObject($listener);
    $this->manager->dispatch('test.priority');
    
    expect($listener->order)->toBe(['first', 'second']);
});

test('can register multiple hooks on same method', function () {
    $count = 0;
    
    $listener = new class {
        public int $count = 0;
        
        #[Hook('event.one')]
        #[Hook('event.two')]
        public function onBothEvents(HookContext $context): void
        {
            $this->count++;
        }
    };
    
    $this->manager->registerObject($listener);
    $this->manager->dispatch('event.one');
    $this->manager->dispatch('event.two');
    
    expect($listener->count)->toBe(2);
});

test('only registers public methods', function () {
    $listener = new class {
        public bool $publicCalled = false;
        public bool $privateCalled = false;
        
        #[Hook('test.public')]
        public function publicMethod(HookContext $context): void
        {
            $this->publicCalled = true;
        }
        
        #[Hook('test.private')]
        private function privateMethod(HookContext $context): void
        {
            $this->privateCalled = true;
        }
    };
    
    $this->manager->registerObject($listener);
    $this->manager->dispatch('test.public');
    $this->manager->dispatch('test.private');
    
    expect($listener->publicCalled)->toBeTrue()
        ->and($listener->privateCalled)->toBeFalse();
});
