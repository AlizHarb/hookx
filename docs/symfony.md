# Symfony Integration

HookX integrates with Symfony via a native Bundle, allowing you to inject the `HookManager` service anywhere in your application.

---

## 1. Installation

### Step 1: Register the Bundle

Add the bundle to your `config/bundles.php` file.

```php
<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    // ...
    AlizHarb\Hookx\Integrations\Symfony\HookXBundle::class => ['all' => true],
];
```

### Step 2: Verify Service

The bundle automatically registers the `HookManager` as a public service. You can verify it by running:

```bash
php bin/console debug:container hookx
```

You should see `AlizHarb\Hookx\HookManager` aliased to `hookx`.

---

## 2. Usage

### In Controllers

You can inject `HookManager` into your controllers.

```php
<?php

namespace App\Controller;

use AlizHarb\Hookx\HookManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(HookManager $hooks): Response
    {
        // Dispatch a hook
        $hooks->dispatch('page.viewed', ['page' => 'home']);

        // Apply a filter
        $title = $hooks->applyFilters('page.title', 'Welcome Home');

        return $this->render('home/index.html.twig', [
            'title' => $title,
        ]);
    }
}
```

### In Services

You can also inject it into any service.

```php
namespace App\Service;

use AlizHarb\Hookx\HookManager;

class OrderService
{
    public function __construct(
        private HookManager $hooks
    ) {}

    public function createOrder(Order $order): void
    {
        // ... save order ...

        $this->hooks->dispatch('order.created', ['order' => $order]);
    }
}
```

---

## 3. Registering Listeners

To register listeners, you currently need to register the object containing the `#[Hook]` attributes with the `HookManager`.

### Step 1: Create Listener Service

```php
namespace App\EventListener;

use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserListener
{
    #[Hook('user.registered')]
    public function onRegistered(HookContext $context): void
    {
        // Handle event
    }
}
```

### Step 2: Register in Kernel/Service

In a standard Symfony app, you can do this in your `Kernel.php` or a dedicated compiler pass. For simplicity, you can also do it in a service subscriber or explicitly in `services.yaml` if you create a factory/configurator (advanced).

**The simplest way (in `Kernel.php` boot method):**

```php
// src/Kernel.php

use AlizHarb\Hookx\HookManager;
use App\EventListener\UserListener;

class Kernel extends BaseKernel
{
    public function boot(): void
    {
        parent::boot();

        $hooks = $this->getContainer()->get('hookx');

        // Register your listeners
        // Note: In a real app, you'd fetch these from the container too
        $hooks->registerObject(new UserListener());
    }
}
```

> **Future Update:** We plan to add a Compiler Pass to automatically scan and register services tagged with `hookx.listener`.

---

## 4. Twig Integration (Manual)

To use HookX in Twig, you can create a Twig Extension.

```php
namespace App\Twig;

use AlizHarb\Hookx\HookManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HookExtension extends AbstractExtension
{
    public function __construct(
        private HookManager $hooks
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('do_hook', [$this->hooks, 'dispatch']),
            new TwigFunction('apply_filters', [$this->hooks, 'applyFilters']),
        ];
    }
}
```

Then in your templates:

```twig
{{ do_hook('footer.render') }}

<h1>{{ apply_filters('page_title', 'Default Title') }}</h1>
```
