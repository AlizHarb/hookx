# Filters

Filters are a powerful mechanism to modify data as it passes through your application. Unlike hooks, which are primarily for side effects (actions), filters are for **transformation**.

## Concept

A filter takes a value, passes it through a series of callbacks, and returns the modified value. This implements the "Pipes and Filters" pattern.

## Defining Filters

Use the `#[Filter]` attribute to register a filter method.

```php
use AlizHarb\Hookx\Attributes\Filter;

class ContentFormatter
{
    #[Filter('content.render')]
    public function addFooter(string $content): string
    {
        return $content . "\n<footer>Powered by HookX</footer>";
    }

    #[Filter('content.render', priority: 5)]
    public function capitalize(string $content): string
    {
        return strtoupper($content); // Runs before addFooter because priority is lower
    }
}
```

## Applying Filters

To run data through the filter chain, use `applyFilters`.

```php
$rawContent = "hello world";
$finalContent = $manager->applyFilters('content.render', $rawContent);

// Result: "HELLO WORLD\n<footer>Powered by HookX</footer>"
```

## Advanced Usage

### Multiple Arguments

Filters can accept additional arguments to help with the transformation logic.

```php
#[Filter('price.calculate')]
public function addTax(float $price, string $country): float
{
    if ($country === 'US') {
        return $price * 1.1;
    }
    return $price * 1.2;
}

// Usage
$price = $manager->applyFilters('price.calculate', 100.0, ['US']);
```

### Breaking the Chain

If a filter returns `null` (and your logic handles it) or throws an exception, the chain can be interrupted. However, by design, filters are expected to return a value of the same type (or compatible) as the input.

## Best Practices

1.  **Idempotency**: Ideally, filters should be pure functions.
2.  **Type Safety**: Always type-hint your filter methods to ensure data integrity.
3.  **Granularity**: Create specific filter names (e.g., `user.name.format` instead of just `user.format`) to avoid conflicts.
