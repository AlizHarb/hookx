<?php

require __DIR__ . '/../src/autoload.php';

use AlizHarb\Hookx\Attributes\Filter;
use AlizHarb\Hookx\HookManager;

class ContentProcessor
{
    #[Filter('content.render', priority: 10)]
    public function sanitize(string $content): string
    {
        return htmlspecialchars($content);
    }

    #[Filter('content.render', priority: 20)]
    public function addParagraphs(string $content): string
    {
        return '<p>' . $content . '</p>';
    }
}

$manager = HookManager::getInstance();
$manager->registerObject(new ContentProcessor());

$raw = "<script>alert('xss')</script>Hello World";
$processed = $manager->applyFilters('content.render', $raw);

echo "<h1>Filter Demo</h1>";
echo "<strong>Original:</strong> " . htmlspecialchars($raw) . "<br>";
echo "<strong>Processed:</strong> " . htmlspecialchars($processed) . "<br>";
echo "<strong>Rendered:</strong> " . $processed;
