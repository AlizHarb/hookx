<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Integrations\Laravel;

use AlizHarb\Hookx\HookManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HookXServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(HookManager::class, function () {
            return HookManager::getInstance();
        });

        $this->app->alias(HookManager::class, 'hookx');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directive: @hook('hook.name', ['arg' => $val])
        Blade::directive('hook', function ($expression) {
            return "<?php app('hookx')->dispatch({$expression}); ?>";
        });
        
        // Register Blade directive for filters: @filter('filter.name', $value)
        Blade::directive('filter', function ($expression) {
            return "<?php echo app('hookx')->applyFilters({$expression}); ?>";
        });
    }
}
