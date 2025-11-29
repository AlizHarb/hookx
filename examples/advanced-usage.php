<?php

require __DIR__ . '/../vendor/autoload.php';

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\Compiler\JITCompiler;
use AlizHarb\Hookx\Sandbox\Sandbox;

$hooks = HookManager::getInstance();

echo "--- Wildcard Hooks ---\n";

$hooks->on('user.*', function (HookContext $context) {
    echo "[Wildcard] Caught event: " . $context->getHookName() . "\n";
});

$hooks->dispatch('user.registered');
$hooks->dispatch('user.deleted');

echo "\n--- JIT Compilation ---\n";

// Define a chain of callbacks
$callbacks = [
    function (HookContext $c) { echo "Step 1\n"; },
    function (HookContext $c) { echo "Step 2\n"; },
    function (HookContext $c) { echo "Step 3\n"; },
];

$compiler = new JITCompiler();
$chain = $compiler->compile($callbacks);

echo "Executing JIT compiled chain...\n";
$chain(new HookContext('jit.test'));

echo "\n--- Sandbox Limits ---\n";

$sandbox = new Sandbox();

echo "Running safe operation...\n";
$sandbox->execute(function () {
    echo "Safe!\n";
}, new HookContext('safe'));

echo "Running slow operation (simulated)...\n";
// Note: This is a soft limit check, so it won't interrupt sleep(), but it will log an error after.
$sandbox->execute(function () {
    usleep(1100000); // 1.1 seconds
}, new HookContext('slow'), timeLimitSeconds: 1);

echo "Check your error log for the time limit warning.\n";
