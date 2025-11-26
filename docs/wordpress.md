# WordPress Integration

Use HookX alongside or as a replacement for WordPress's native hook system.

---

## Plugin Integration

Create a WordPress plugin that uses HookX:

```php
<?php
/**
 * Plugin Name: HookX Integration
 * Description: Modern hook system for WordPress
 * Version: 1.0.0
 */

require_once __DIR__ . '/vendor/autoload.php';

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Attributes\Hook;

class MyPluginHooks
{
    #[Hook('post.published')]
    public function onPostPublished($ctx): void
    {
        $post = $ctx->getArgument('post');
        // Handle post publication
    }
}

// Initialize
$hooks = HookManager::getInstance();
$hooks->registerObject(new MyPluginHooks());

// Dispatch on WordPress action
add_action('publish_post', function ($post_id) use ($hooks) {
    $post = get_post($post_id);
    $hooks->dispatch('post.published', ['post' => $post]);
});
```

## Bridging WordPress Hooks

Bridge WordPress actions and filters to HookX:

```php
// Bridge WordPress action to HookX
add_action('init', function () use ($hooks) {
    $hooks->dispatch('wordpress.init');
});

// Bridge WordPress filter to HookX
add_filter('the_content', function ($content) use ($hooks) {
    return $hooks->applyFilters('content.render', $content);
});
```

More documentation coming soon...
