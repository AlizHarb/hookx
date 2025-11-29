<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Console;

use AlizHarb\Hookx\HookManager;

class Application
{
    /**
     * Run the console application.
     *
     * @param array<int, string> $argv
     * @return void
     */
    public function run(array $argv): void
    {
        $command = $argv[1] ?? 'help';

        switch ($command) {
            case 'list':
                $this->listHooks();
                break;
            case 'make:hook':
                $name = $argv[2] ?? 'example';
                (new Commands\MakeHookCommand())->execute($name);
                break;
            case 'repl':
                (new Commands\ReplCommand())->execute();
                break;
            case 'help':
            default:
                $this->showHelp();
                break;
        }
    }

    private function listHooks(): void
    {
        echo "Registered Hooks:\n";
        echo "-----------------\n";
        
        // Since HookManager is a singleton and we are in a fresh process, 
        // this will only show hooks registered in THIS process (which is empty).
        // In a real app, this CLI would need to bootstrap the user's application.
        // For now, we will just show a message explaining this.
        
        echo "No hooks registered in this CLI session.\n";
        echo "To debug your application hooks, you need to bootstrap your app.\n";
    }

    private function showHelp(): void
    {
        echo "HookX CLI Tool\n\n";
        echo "Usage: bin/hookx [command]\n\n";
        echo "Commands:\n";
        echo "  list    List registered hooks\n";
        echo "  help    Show this help message\n";
    }
}
