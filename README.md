# HookX

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alizharb/hookx.svg?style=flat-square)](https://packagist.org/packages/alizharb/hookx)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/AlizHarb/hookx/tests.yml?label=tests)](https://github.com/AlizHarb/hookx/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alizharb/hookx.svg?style=flat-square)](https://packagist.org/packages/alizharb/hookx)
[![License](https://img.shields.io/packagist/l/alizharb/hookx.svg?style=flat-square)](https://packagist.org/packages/alizharb/hookx)

**HookX** is a next-generation, high-performance PHP hook and event system designed for modern applications. Built on PHP 8.3+, it leverages **Attributes**, **Fibers**, and **Strong Typing** to deliver a developer experience that is both powerful and elegant.

## ✨ Features

- **Zero-Dependency**: Pure PHP implementation, no external bloat.
- **Async & Background Hooks**: Run hooks in Fibers or push them to a Queue (Redis/Sync) using `#[Async]` and `#[Background]` attributes.
- **Framework Integrations**: Native support for **Laravel** and **Symfony**.
- **Developer Experience**: CLI tool (`bin/hookx`), REPL, and global helpers (`hook()`, `filter()`).
- **Advanced Matching**: Wildcards (`user.*`) and Regex (`#^order\.(created|updated)$#`) support.
- **Safety**: Immutable context, strict mode, and sandbox execution with time/memory limits.
- **Performance**: JIT compilation (experimental) and Zero-Copy dispatching.

## 📋 Requirements

- PHP 8.3 or higher

## 📦 Installation

Install the package via Composer:

```bash
composer require alizharb/hookx
```

## Documentation

- [**Basics**](docs/basics.md): Core concepts, HookManager, and Priorities.
- [**Async & Queue**](docs/async.md): Background processing and Fibers.
- [**Advanced Usage**](docs/advanced.md): Wildcards, Regex, JIT, and Sandbox.
- [**CLI Tooling**](docs/cli.md): Using the `hookx` command-line tool.
- **Integrations**:
  - [**Laravel**](docs/laravel.md)
  - [**Symfony**](docs/symfony.md)
  - [**WordPress**](docs/wordpress.md)

## Quick Start

### Installation

```bash
composer require alizharb/hookx
```

### Basic Usage

```php
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserListener
{
    #[Hook('user.registered')]
    public function onRegister(HookContext $context): void
    {
        echo "User registered: " . $context->getArgument('email');
    }
}

// Register
$manager->registerObject(new UserListener());

// Dispatch
$manager->dispatch('user.registered', [
    'user' => ['name' => 'Alice', 'email' => 'alice@example.com']
]);
```

## ⚡ Async & Background Hooks

HookX supports both concurrent execution (Fibers) and true background processing (Queues).

```php
use AlizHarb\Hookx\Async\AsyncHookDispatcher;

$dispatcher = new AsyncHookDispatcher($manager);

// Dispatches in background using Fibers
$dispatcher->dispatchAsync('email.send', [
    'to' => 'user@example.com',
    'subject' => 'Welcome!'
]);
```

For true background processing via Redis, check the [Async Documentation](docs/async.md).

## 📚 Documentation

For comprehensive documentation, check the `docs/` directory:

- [Introduction](docs/introduction.md)
- [Installation](docs/installation.md)
- [Basic Usage](docs/basics.md)
- [Advanced Usage](docs/advanced.md)
- [CLI Tooling](docs/cli.md)
- [Async & Background Hooks](docs/async.md)
- [Filters](docs/filters.md)
- [Framework Integrations](docs/integrations.md)

## 🧪 Testing

Run the test suite:

```bash
composer test
```

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

<div align="center">

**Made with ❤️ by [Ali Harb](https://github.com/AlizHarb)**

</div>
