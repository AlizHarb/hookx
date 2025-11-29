<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Console\Commands;

use AlizHarb\Hookx\HookManager;

class ReplCommand
{
    /**
     * Execute the REPL loop.
     *
     * @return void
     */
    public function execute(): void
    {
        echo "HookX Interactive REPL\n";
        echo "Type 'exit' to quit.\n";

        $manager = HookManager::getInstance();
        $stdin = fopen('php://stdin', 'r');
        if ($stdin === false) {
            echo "Error: Could not open stdin.\n";
            return;
        }

        while (true) {
            echo "> ";
            $input = fgets($stdin);
            
            if ($input === false) {
                break;
            }

            $line = trim($input);

            if ($line === 'exit') {
                break;
            }

            if (empty($line)) {
                continue;
            }

            try {
                // Very basic eval loop for demonstration
                // In production, use PsySH
                eval($line . ';');
            } catch (\Throwable $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }

        fclose($stdin);
    }
}
