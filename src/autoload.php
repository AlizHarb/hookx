<?php

declare(strict_types=1);

/**
 * Hookx Standalone Autoloader
 *
 * PSR-4 compliant autoloader for using Hookx without Composer.
 * Include this file in your project to automatically load Hookx classes.
 *
 * @package AlizHarb\Hookx
 * @author  Ali Harb <harbzali@gmail.com>
 * @license MIT
 * @link    https://github.com/AlizHarb/hookx
 *
 * @example
 * ```php
 * require 'path/to/hookx/src/autoload.php';
 * use AlizHarb\Hookx\HookManager;
 * $manager = HookManager::getInstance();
 * ```
 */

spl_autoload_register(function ($class) {
    $prefix = 'AlizHarb\\Hookx\\';
    $base_dir = __DIR__ . '/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
