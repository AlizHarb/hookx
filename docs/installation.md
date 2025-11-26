# Installation

## Requirements

- PHP 8.5 or higher
- Composer (optional)

## Via Composer

The recommended way to install Hookx is via Composer:

```bash
composer require alizharb/hookx
```

## Standalone

If you are not using Composer, you can download the source code and include the autoloader manually:

```php
require '/path/to/hookx/src/autoload.php';
```

## Configuration

Hookx works out of the box with zero configuration. However, you can configure the `HookManager` singleton if needed.

```php
use AlizHarb\Hookx\HookManager;

$manager = HookManager::getInstance();
```
