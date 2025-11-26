# Introduction

**Hookx** is a high-performance, extensible hook and event system designed for modern PHP 8.3+ applications.

## Philosophy

Hookx was built with three core principles in mind:

1.  **Zero Overhead**: If you don't use it, it shouldn't slow you down.
2.  **Developer Experience**: Attributes make registration declarative and clean.
3.  **Modernity**: Leveraging PHP 8.3+ features like Fibers and Readonly Properties.

## Why Hookx?

Traditional event dispatchers often rely on string-based configuration or complex listener providers. Hookx simplifies this by allowing you to annotate your methods directly.

```php
#[Hook('user.registered')]
public function onRegister(HookContext $context) { ... }
```

This approach keeps your logic close to your code and makes it easy to understand what's happening.
