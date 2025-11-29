# Installation

Getting started with HookX is simple.

## Composer

Install the package via Composer:

```bash
composer require alizharb/hookx
```

## Configuration

HookX is designed to work out of the box with zero configuration for basic usage. However, for advanced features like Background Hooks, you may need to configure your drivers.

### Setting up Redis (Optional)

If you plan to use the Redis driver for background hooks, ensure you have the `redis` extension installed and a running Redis instance.

```bash
pecl install redis
```

## Integration

### Standalone

```php
use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();
```

### Laravel

Publish the configuration file (coming soon in v1.1.0):

```bash
php artisan vendor:publish --tag=hookx-config
```

### Symfony

Register the bundle in `config/bundles.php` (coming soon in v1.1.0).

## Next Steps

Once installed, head over to the [Basic Usage](basics.md) guide to write your first hook.
