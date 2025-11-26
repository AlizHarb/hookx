<?php

require __DIR__ . '/../src/autoload.php';

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\Context\HookContext;
use AlizHarb\Hookx\HookManager;

// 1. Define a Listener Class
class DemoListener
{
    #[Hook('app.init')]
    public function onInit(HookContext $context): void
    {
        echo "[Hook] App Initialized at " . $context->getArgument('time') . "\n";
    }

    #[Filter('app.message')]
    public function uppercaseMessage(string $message): string
    {
        return strtoupper($message);
    }
}

// 2. Setup Manager
$manager = HookManager::getInstance();
$manager->registerObject(new DemoListener());

// 3. Dispatch Hook
echo "Dispatching 'app.init'...\n";
$manager->dispatch('app.init', ['time' => date('H:i:s')]);

// 4. Apply Filter
echo "\nApplying 'app.message' filter...\n";
$message = "hello world";
$filtered = $manager->applyFilters('app.message', $message);
echo "Original: $message\n";
echo "Filtered: $filtered\n";
