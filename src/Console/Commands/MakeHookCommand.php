<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Console\Commands;

class MakeHookCommand
{
    public function execute(string $name): void
    {
        $className = ucfirst($name) . 'Listener';
        $content = <<<PHP
<?php

namespace App\Listeners;

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class {$className}
{
    #[Hook('{$name}.event')]
    public function handle(HookContext \$context): void
    {
        // TODO: Implement listener logic
    }
}
PHP;

        echo "Generated {$className}.php\n";
        echo $content . "\n";
    }
}
