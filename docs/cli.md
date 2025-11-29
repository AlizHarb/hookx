# CLI Tooling

HookX comes with a powerful CLI tool to help you develop and debug your hooks.

## Usage

The binary is located at `bin/hookx`.

```bash
./bin/hookx [command]
```

## Commands

### `list`

Lists all registered hooks.

```bash
./bin/hookx list
```

> **Note:** Since the CLI runs in a separate process, it will only show hooks registered during the CLI's bootstrap. To see your application's hooks, you would need to include your app's bootstrap logic in the CLI runner (custom integration required).

### `make:hook`

Generates a new Listener class with the `#[Hook]` attribute.

```bash
./bin/hookx make:hook UserRegistered
```

This will output the code for `UserRegisteredListener.php`.

### `repl`

Starts an interactive Read-Eval-Print Loop (REPL) where you can test HookX commands in real-time.

```bash
./bin/hookx repl
```

**Example Session:**

```
> use AlizHarb\Hookx\HookManager;
> $h = HookManager::getInstance();
> $h->on('test', fn() => print("Worked!\n"));
> $h->dispatch('test');
Worked!
```
