# Laravel Integration

HookX integrates seamlessly with Laravel, providing a powerful event system that complements Laravel's native events.

---

## Service Provider Setup

Create a service provider to register HookX:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use AlizHarb\Hookx\HookManager;

class HookXServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HookManager::class, function () {
            return HookManager::getInstance();
        });
    }

    public function boot(): void
    {
        // Register your hook listeners here
    }
}
```

Register the service provider in `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\HookXServiceProvider::class,
],
```

---

## Creating Listeners

Create a listener class using attributes:

```php
<?php

namespace App\Listeners;

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserEventListener
{
    #[Hook('user.registered', priority: 10)]
    public function onUserRegistered(HookContext $ctx): void
    {
        $user = $ctx->getArgument('user');

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }

    #[Hook('user.login', priority: 5)]
    public function onUserLogin(HookContext $ctx): void
    {
        $user = $ctx->getArgument('user');

        // Log user activity
        activity()
            ->causedBy($user)
            ->log('User logged in');
    }
}
```

---

## Registering Listeners

Register your listeners in the service provider:

```php
public function boot(): void
{
    $hooks = app(HookManager::class);

    // Register listener object
    $hooks->registerObject(new UserEventListener());
}
```

---

## Using in Controllers

Dispatch hooks from your controllers:

```php
<?php

namespace App\Http\Controllers;

use AlizHarb\Hookx\HookManager;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create($request->validated());

        // Dispatch hook
        app(HookManager::class)->dispatch('user.registered', [
            'user' => $user
        ]);

        return response()->json($user, 201);
    }
}
```

---

## Bridging Laravel Events

You can bridge Laravel events to HookX:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use AlizHarb\Hookx\HookManager;

class HookXServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $hooks = app(HookManager::class);

        // Bridge Laravel events to HookX
        Event::listen('eloquent.created: App\Models\User', function ($user) use ($hooks) {
            $hooks->dispatch('user.created', ['user' => $user]);
        });
    }
}
```

---

## Using Filters

Apply filters to transform data:

```php
use AlizHarb\Hookx\Attributes\Filter;

class ContentFilter
{
    #[Filter('content.render')]
    public function sanitizeContent(string $content): string
    {
        return strip_tags($content, '<p><a><strong><em>');
    }

    #[Filter('content.render', priority: 20)]
    public function addReadingTime(string $content): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = ceil($wordCount / 200);

        return "<div class='reading-time'>{$readingTime} min read</div>" . $content;
    }
}
```

Apply the filter:

```php
$hooks = app(HookManager::class);
$hooks->registerObject(new ContentFilter());

$content = $hooks->applyFilters('content.render', $rawContent);
```

---

## Complete Example

Here's a complete example of using HookX in a Laravel application:

```php
<?php

namespace App\Services;

use AlizHarb\Hookx\HookManager;
use AlizHarb\Hookx\Attributes\{Hook, Filter};
use App\Models\{User, Post};
use Illuminate\Support\Facades\Mail;

class BlogService
{
    public function __construct(
        private HookManager $hooks
    ) {
        $this->hooks->registerObject($this);
    }

    #[Hook('post.published')]
    public function notifySubscribers($ctx): void
    {
        $post = $ctx->getArgument('post');
        $subscribers = User::where('subscribed', true)->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)
                ->send(new NewPostNotification($post));
        }
    }

    #[Filter('post.content')]
    public function processShortcodes(string $content): string
    {
        // Process custom shortcodes
        return preg_replace_callback('/\[gallery\]/', function () {
            return view('shortcodes.gallery')->render();
        }, $content);
    }

    public function publishPost(Post $post): void
    {
        $post->status = 'published';
        $post->published_at = now();

        // Apply content filters
        $post->content = $this->hooks->applyFilters('post.content', $post->content);

        $post->save();

        // Dispatch hook
        $this->hooks->dispatch('post.published', ['post' => $post]);
    }
}
```

---

## Best Practices

1. **Use Dependency Injection**: Inject `HookManager` via Laravel's container
2. **Register Early**: Register listeners in service providers
3. **Use Attributes**: Leverage PHP 8 attributes for clean code
4. **Bridge Events**: Connect Laravel events with HookX for consistency
5. **Test Hooks**: Write tests for your hook listeners

---

## Testing

Test your hooks in PHPUnit:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use AlizHarb\Hookx\HookManager;
use App\Models\User;

class UserHooksTest extends TestCase
{
    public function test_user_registration_hook_fires()
    {
        $hooks = app(HookManager::class);
        $fired = false;

        $hooks->on('user.registered', function ($ctx) use (&$fired) {
            $fired = true;
        });

        $user = User::factory()->create();
        $hooks->dispatch('user.registered', ['user' => $user]);

        $this->assertTrue($fired);
    }
}
```
