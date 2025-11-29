<?php

declare(strict_types=1);

namespace AlizHarb\Hookx;

/**
 * Class Priority
 *
 * Predefined constants for hook and filter priorities.
 *
 * @package AlizHarb\Hookx
 */
class Priority
{
    /**
     * Highest priority. Runs first.
     */
    public const HIGHEST = 0;

    /**
     * High priority.
     */
    public const HIGH = 5;

    /**
     * Normal priority (default).
     */
    public const NORMAL = 10;

    /**
     * Low priority.
     */
    public const LOW = 20;

    /**
     * Lowest priority. Runs last.
     */
    public const LOWEST = 100;
}
