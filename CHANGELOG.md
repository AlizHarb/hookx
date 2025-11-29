# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-11-29

### Added

- **Background Hooks**: Added `#[Background]` attribute and Queue system (`SyncDriver`, `RedisDriver`) for offloading hook execution.
- **Async Hooks**: Added `#[Async]` attribute for non-blocking execution using PHP Fibers.
- **Integrations**: Native support for **Laravel** (ServiceProvider, Facade, Blade) and **Symfony** (Bundle).
- **Helpers**: Added global `hook()` and `filter()` helper functions.
- **Advanced Matching**: Support for Wildcard (`user.*`) and Regex (`#^order\.(created|updated)$#`) hook names.
- **Strict Mode**: Added `setStrictMode()` to throw exceptions when no listeners are found.
- **Sandbox Limits**: Added time and memory limits to `Sandbox` execution.
- **JIT Compilation**: Experimental `JITCompiler` for optimizing hook chains.
- **Zero-Copy Dispatch**: Experimental trait for memory-efficient dispatching.
- **CLI Tool**: Added `bin/hookx` with `list`, `make:hook`, and `repl` commands.
- **Priorities**: Added `Priority` constants class.
- **Immutable Context**: Added `ImmutableHookContext` and `with()` method for context updates.

### Changed

- Updated `HookManager::dispatch` to return `HookContext` instead of void.
- Updated documentation structure and added new pages for Async, CLI, and Advanced features.

## [1.0.0] - 2025-11-28

### Added

- Initial release.
- Core `HookManager` for registering and dispatching hooks.
- Attribute-based registration (`#[Hook]`, `#[Filter]`).
- `HookContext` for passing data between listeners.
- Basic `Sandbox` for error handling.
- `AsyncHookDispatcher` (Fiber-based).
