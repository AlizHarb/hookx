<?php

use AlizHarb\Hookx\Compiler\JITCompiler;
use AlizHarb\Hookx\Context\HookContext;

test('jit compiler creates executable chain', function () {
    $compiler = new JITCompiler();
    $log = [];

    $callbacks = [
        function (HookContext $c) use (&$log) { $log[] = 'A'; },
        function (HookContext $c) use (&$log) { $log[] = 'B'; },
        function (HookContext $c) use (&$log) { $log[] = 'C'; },
    ];

    $chain = $compiler->compile($callbacks);

    expect($chain)->toBeInstanceOf(Closure::class);

    $chain(new HookContext('test'));

    expect($log)->toBe(['A', 'B', 'C']);
});

test('jit chain stops propagation', function () {
    $compiler = new JITCompiler();
    $log = [];

    $callbacks = [
        function (HookContext $c) use (&$log) {
            $log[] = 'A';
            $c->stopPropagation();
        },
        function (HookContext $c) use (&$log) { $log[] = 'B'; },
    ];

    $chain = $compiler->compile($callbacks);
    $chain(new HookContext('test'));

    expect($log)->toBe(['A']);
});
