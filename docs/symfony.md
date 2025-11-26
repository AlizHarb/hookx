# Symfony Integration

Integrate HookX with Symfony for a powerful event-driven architecture.

---

## Bundle Configuration

Create a bundle to integrate HookX with Symfony.

---

## Service Registration

Register HookX as a service in `config/services.yaml`:

```yaml
services:
  AlizHarb\Hookx\HookManager:
    factory: ['AlizHarb\Hookx\HookManager', "getInstance"]
    public: true
```

## Creating Listeners

Create listener services:

```php
<?php

namespace App\EventListener;

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserListener
{
    #[Hook('user.created')]
    public function onUserCreated(HookContext $ctx): void
    {
        $user = $ctx->getArgument('user');
        // Handle user creation
    }
}
```

## Using in Controllers

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use AlizHarb\Hookx\HookManager;

class UserController extends AbstractController
{
    public function create(HookManager $hooks)
    {
        // Create user logic

        $hooks->dispatch('user.created', ['user' => $user]);

        return $this->json($user);
    }
}
```

More documentation coming soon...
