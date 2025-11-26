# Filters

## Introduction

Filters in HookX allow you to modify data as it passes through your application. Unlike hooks (which perform actions), filters transform and return values.

## Basic Filter Usage

### Simple Filter

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Register a filter
$hooks->addFilter('content.render', function(string $content) {
    // Add a footer to all content
    return $content . "\n\n---\nPowered by HookX";
});

// Apply the filter
$content = "Hello, World!";
$filtered = $hooks->applyFilters('content.render', $content);

echo $filtered;
// Output:
// Hello, World!
//
// ---
// Powered by HookX
```

---

## Filter Chaining

Filters can be chained together, with each filter receiving the output of the previous one.

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// First filter: Convert to uppercase
$hooks->addFilter('text.transform', function(string $text) {
    return strtoupper($text);
}, priority: 10);

// Second filter: Add exclamation
$hooks->addFilter('text.transform', function(string $text) {
    return $text . '!';
}, priority: 20);

// Third filter: Wrap in brackets
$hooks->addFilter('text.transform', function(string $text) {
    return "[{$text}]";
}, priority: 30);

$result = $hooks->applyFilters('text.transform', 'hello');

echo $result;
// Output: [HELLO!]
```

**Execution Order:**

1. `hello` → `HELLO` (priority 10)
2. `HELLO` → `HELLO!` (priority 20)
3. `HELLO!` → `[HELLO!]` (priority 30)

---

## Using Filter Attributes

```php
<?php

use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\HookManager;

class ContentFilters
{
    #[Filter('content.render')]
    public function addReadingTime(string $content): string
    {
        $wordCount = str_word_count($content);
        $readingTime = ceil($wordCount / 200); // 200 words per minute

        return "📖 {$readingTime} min read\n\n" . $content;
    }

    #[Filter('content.render', priority: 20)]
    public function addTableOfContents(string $content): string
    {
        // Extract headings and create TOC
        preg_match_all('/<h[1-6]>(.*?)<\/h[1-6]>/i', $content, $matches);

        if (empty($matches[1])) {
            return $content;
        }

        $toc = "## Table of Contents\n";
        foreach ($matches[1] as $heading) {
            $toc .= "- {$heading}\n";
        }

        return $toc . "\n" . $content;
    }
}

// Register all filters
$manager = HookManager::getInstance();
$manager->registerObject(new ContentFilters());

// Apply filters
$content = "<h1>Introduction</h1><p>Lorem ipsum...</p>";
$filtered = $manager->applyFilters('content.render', $content);
```

---

## Passing Additional Arguments

Filters can accept additional arguments beyond the value being filtered.

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Filter with additional arguments
$hooks->addFilter('price.calculate', function(
    float $price,
    float $taxRate,
    float $discount
) {
    $priceWithTax = $price * (1 + $taxRate);
    $finalPrice = $priceWithTax - $discount;

    return max(0, $finalPrice); // Never negative
});

// Apply filter with additional arguments
$basePrice = 100.00;
$finalPrice = $hooks->applyFilters('price.calculate', $basePrice, [
    0.20,  // 20% tax
    15.00  // $15 discount
]);

echo "Final price: \$" . number_format($finalPrice, 2);
// Output: Final price: $105.00
```

---

## Real-World Examples

### 1. Content Sanitization

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Remove dangerous HTML
$hooks->addFilter('user.input', function(string $input) {
    return strip_tags($input, '<p><br><strong><em>');
}, priority: 5);

// Trim whitespace
$hooks->addFilter('user.input', function(string $input) {
    return trim($input);
}, priority: 10);

// Convert URLs to links
$hooks->addFilter('user.input', function(string $input) {
    return preg_replace(
        '/(https?:\/\/[^\s]+)/',
        '<a href="$1">$1</a>',
        $input
    );
}, priority: 15);

// Usage
$userInput = "  <script>alert('xss')</script>Check out https://example.com  ";
$safe = $hooks->applyFilters('user.input', $userInput);

echo $safe;
// Output: Check out <a href="https://example.com">https://example.com</a>
```

### 2. Image URL Transformation

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Add CDN prefix
$hooks->addFilter('image.url', function(string $url) {
    if (strpos($url, 'http') === 0) {
        return $url; // Already absolute
    }

    return 'https://cdn.example.com' . $url;
});

// Add image optimization parameters
$hooks->addFilter('image.url', function(string $url, int $width = 0, int $height = 0) {
    if ($width > 0 || $height > 0) {
        $params = http_build_query([
            'w' => $width,
            'h' => $height,
            'fit' => 'crop'
        ]);

        return $url . '?' . $params;
    }

    return $url;
});

// Usage
$imageUrl = '/uploads/photo.jpg';
$optimized = $hooks->applyFilters('image.url', $imageUrl, [800, 600]);

echo $optimized;
// Output: https://cdn.example.com/uploads/photo.jpg?w=800&h=600&fit=crop
```

### 3. Data Formatting

```php
<?php

use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\HookManager;

class DataFormatters
{
    #[Filter('api.response')]
    public function addMetadata(array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'timestamp' => time(),
                'version' => '1.0'
            ]
        ];
    }

    #[Filter('api.response', priority: 20)]
    public function camelCaseKeys(array $data): array
    {
        return $this->convertKeys($data, 'camelCase');
    }

    private function convertKeys(array $array, string $case): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $case === 'camelCase'
                ? lcfirst(str_replace('_', '', ucwords($key, '_')))
                : $key;

            $result[$newKey] = is_array($value)
                ? $this->convertKeys($value, $case)
                : $value;
        }

        return $result;
    }
}

$manager = HookManager::getInstance();
$manager->registerObject(new DataFormatters());

$response = [
    'user_id' => 123,
    'first_name' => 'John',
    'last_name' => 'Doe'
];

$formatted = $manager->applyFilters('api.response', $response);

print_r($formatted);
// Output:
// Array
// (
//     [data] => Array
//         (
//             [userId] => 123
//             [firstName] => John
//             [lastName] => Doe
//         )
//     [meta] => Array
//         (
//             [timestamp] => 1732630000
//             [version] => 1.0
//         )
// )
```

---

## Advanced Patterns

### Conditional Filtering

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

$hooks->addFilter('email.subject', function(string $subject, string $type) {
    // Add prefix based on email type
    $prefixes = [
        'notification' => '🔔',
        'alert' => '⚠️',
        'success' => '✅',
        'error' => '❌'
    ];

    $prefix = $prefixes[$type] ?? '';

    return $prefix ? "{$prefix} {$subject}" : $subject;
});

// Usage
$subject = $hooks->applyFilters('email.subject', 'Payment Received', ['success']);
echo $subject;
// Output: ✅ Payment Received
```

### Type Transformation

```php
<?php

use AlizHarb\Hookx\HookManager;

$hooks = HookManager::getInstance();

// Convert CSV string to array
$hooks->addFilter('data.import', function(string $csv) {
    return array_map('str_getcsv', explode("\n", trim($csv)));
});

// Validate and clean data
$hooks->addFilter('data.import', function(array $rows) {
    return array_filter($rows, function($row) {
        return count($row) >= 3; // Must have at least 3 columns
    });
});

// Convert to objects
$hooks->addFilter('data.import', function(array $rows) {
    return array_map(function($row) {
        return (object)[
            'name' => $row[0] ?? '',
            'email' => $row[1] ?? '',
            'phone' => $row[2] ?? ''
        ];
    }, $rows);
});

// Usage
$csv = "John Doe,john@example.com,555-1234\nJane Smith,jane@example.com,555-5678";
$users = $hooks->applyFilters('data.import', $csv);

foreach ($users as $user) {
    echo "{$user->name}: {$user->email}\n";
}
```

---

## Best Practices

### 1. Always Return a Value

```php
// ❌ Bad - doesn't return
$hooks->addFilter('text.uppercase', function(string $text) {
    strtoupper($text); // Missing return!
});

// ✅ Good
$hooks->addFilter('text.uppercase', function(string $text) {
    return strtoupper($text);
});
```

### 2. Maintain Type Consistency

```php
// ❌ Bad - changes type
$hooks->addFilter('user.data', function(array $user) {
    return json_encode($user); // Returns string instead of array
});

// ✅ Good - maintains type
$hooks->addFilter('user.data', function(array $user) {
    $user['processed'] = true;
    return $user; // Still an array
});
```

### 3. Document Expected Types

```php
/**
 * Filter user data before saving
 *
 * @filter user.before_save
 * @param array $user User data array
 * @param int $userId User ID
 * @return array Modified user data
 */
$userData = $hooks->applyFilters('user.before_save', $userData, [$userId]);
```

### 4. Handle Edge Cases

```php
$hooks->addFilter('text.excerpt', function(?string $text, int $length = 100) {
    if ($text === null || $text === '') {
        return '';
    }

    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . '...';
});
```

---

## Performance Tips

### 1. Avoid Heavy Operations

```php
// ❌ Bad - database query in filter
$hooks->addFilter('user.name', function(string $name, int $userId) {
    $user = DB::find($userId); // Slow!
    return $user->display_name;
});

// ✅ Good - pass data as argument
$hooks->addFilter('user.name', function(string $name, array $userData) {
    return $userData['display_name'] ?? $name;
});
```

### 2. Cache Expensive Computations

```php
$hooks->addFilter('content.processed', function(string $content) {
    static $cache = [];

    $hash = md5($content);

    if (isset($cache[$hash])) {
        return $cache[$hash];
    }

    // Expensive operation
    $processed = processMarkdown($content);

    $cache[$hash] = $processed;

    return $processed;
});
```

---

## Next Steps

- Learn about [Async Hooks](async.md) for non-blocking operations
- See [Framework Integrations](integrations.md) for Laravel, Symfony, etc.
- Explore [Basic Hooks](basics.md) for action-based hooks
