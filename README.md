# HookX

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alizharb/hookx.svg?style=flat-square)](https://packagist.org/packages/alizharb/hookx)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/AlizHarb/hookx/tests.yml?label=tests)](https://github.com/AlizHarb/hookx/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alizharb/hookx.svg?style=flat-square)](https://packagist.org/packages/alizharb/hookx)
[![License](https://img.shields.io/packagist/l/alizharb/hookx.svg?style=flat-square)](https://packagist.org/packages/alizharb/hookx)

**HookX** is a next-generation, high-performance PHP hook and event system designed for modern applications. Built on PHP 8.3+, it leverages **Attributes**, **Fibers**, and **Strong Typing** to deliver a developer experience that is both powerful and elegant.

## ✨ Features

- 🎯 **Attribute-Based Registration** - Register hooks declaratively using `#[Hook]` and `#[Filter]`
- ⚡ **Async Hooks** - Non-blocking execution using native PHP Fibers
- 🔧 **Powerful Filter System** - Transform data with robust filter pipelines
- 🛡️ **Sandboxed Execution** - Safe execution of third-party code with error containment
- 🔌 **Framework Agnostic** - Seamless integration with Laravel, Symfony, WordPress, and more
- 📦 **Zero Dependencies** - Lightweight and fast with no external bloat
- 🔄 **Context Awareness** - Pass rich, immutable context objects to every listener
- 🛑 **Propagation Control** - Halt hook chains conditionally with `stopPropagation()`
- 🔍 **Audit Logging** - Trace execution flow for advanced debugging
- ✅ **Fully Tested** - Comprehensive test suite ensuring stability

## 📋 Requirements

- PHP 8.3 or higher

## 📦 Installation

Install the package via Composer:

```bash
composer require alizharb/hookx
```

## 🚀 Quick Start

### 1. Define a Listener

Use attributes to define your hooks directly on your class methods:

```php
use AlizHarb\Hookx\Attributes\Hook;
use AlizHarb\Hookx\Context\HookContext;

class UserListener
{
    #[Hook('user.registered')]
    public function onRegister(HookContext $context): void
    {
        $user = $context->getArgument('user');
        echo "Welcome, {$user['name']}!";
    }
}
```

### 2. Register and Dispatch

Initialize the manager, register your listener, and dispatch an event:

```php
use AlizHarb\Hookx\HookManager;

// Initialize
$manager = HookManager::getInstance();

// Register
$manager->registerObject(new UserListener());

// Dispatch
$manager->dispatch('user.registered', [
    'user' => ['name' => 'Alice', 'email' => 'alice@example.com']
]);
```

## 📚 Documentation

For comprehensive documentation, check the `docs/` directory or view our examples.

### Key Topics

- [Basic Usage](docs/basics.md)
- [Async Hooks](docs/async.md)
- [Filters](docs/filters.md)
- [Framework Integrations](docs/integrations.md)

## ⚡ Async Hooks

HookX supports non-blocking asynchronous execution using PHP Fibers. This is perfect for heavy tasks like sending emails or making API calls without slowing down the main request.

```php
use AlizHarb\Hookx\Async\AsyncHookDispatcher;

$dispatcher = new AsyncHookDispatcher($manager);

// Dispatches in background, returns immediately
$dispatcher->dispatchAsync('email.send', [
    'to' => 'user@example.com',
    'subject' => 'Welcome!'
]);
```

## 🔧 Filters

Filters allow you to modify data as it passes through your application. Unlike hooks, filters always return a value.

```php
use AlizHarb\Hookx\Attributes\Filter;

class ContentFilter
{
    #[Filter('content.render', priority: 10)]
    public function addFooter(string $content): string
    {
        return $content . "\n<footer>Powered by HookX</footer>";
    }
}

// Apply filters
$content = $manager->applyFilters('content.render', 'Hello World');
```

## 🔌 Integrations

HookX is designed to work anywhere, but we provide first-class support for popular ecosystems:

- **[Laravel](docs/laravel.md)**: Service Provider, Facade, and Event Bridge.
- **[Symfony](docs/symfony.md)**: Bundle configuration and Event Listener integration.
- **[WordPress](docs/wordpress.md)**: Modernize your plugins with object-oriented hooks.

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

Run static analysis:

```bash
composer analyse
```

## 🔒 Security

If you discover any security-related issues, please email harbzali@gmail.com instead of using the issue tracker.

## 👥 Credits

- [Ali Harb](https://github.com/AlizHarb)
- [All Contributors](../../contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 💖 Support

If you find this package helpful, please consider:

- ⭐ Starring the repository
- 🐛 Reporting bugs
- 💡 Suggesting new features
- 📖 Improving documentation

---

<div align="center">

**Made with ❤️ by [Ali Harb](https://github.com/AlizHarb)**

</div>
