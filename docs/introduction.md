# Introduction

Welcome to **HookX**, the next-generation hook and event system for PHP.

HookX is designed to be the backbone of extensible applications. Whether you are building a modular CMS, a plugin-based architecture, or simply need to decouple your business logic, HookX provides the tools you need with zero bloat.

## Why HookX?

### 🚀 High Performance

Built on **PHP 8.3+**, HookX leverages the latest language features including Attributes, Fibers, and strong typing. It is optimized for speed, ensuring that your event dispatching adds negligible overhead to your application.

### 🧠 Developer Experience

We believe that developer tools should be intuitive. HookX uses **Attributes** (`#[Hook]`, `#[Filter]`) to make registration declarative and co-located with your logic. No more massive configuration arrays or scattered `add_action` calls.

### ⚡ Async & Background Processing

Modern applications need to be fast. HookX supports:

- **Async Hooks**: Non-blocking execution using PHP Fibers for concurrent tasks.
- **Background Hooks**: True background processing via Redis or other queue drivers, allowing you to offload heavy tasks like email sending or image processing entirely.

### 🛡️ Robust & Safe

- **Type Safety**: Fully typed codebase.
- **Sandboxing**: Error containment to prevent one bad hook from crashing your entire app.
- **Context Awareness**: Pass rich `HookContext` objects to listeners, giving them control over propagation and data flow.

## Core Concepts

- **Hooks**: Actions that happen at specific points (e.g., `user.registered`). Listeners can perform side effects.
- **Filters**: Data transformation pipelines. Listeners receive a value and return a modified version.
- **Context**: The state passed to hooks, containing arguments and control methods.
- **Dispatcher**: The engine that manages registration and execution.

## Requirements

- **PHP**: 8.3 or higher
- **Extensions**: `json` (for queue drivers), `redis` (optional, for Redis driver)

## License

HookX is open-sourced software licensed under the [MIT license](LICENSE).
