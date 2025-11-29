<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Attributes;

use Attribute;

/**
 * Attribute Hook
 *
 * Marks a method as a hook listener.
 *
 * @package AlizHarb\Hookx\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Hook
{
    /**
     * @param string $name     The name of the hook to listen for.
     * @param int    $priority The execution priority (lower runs earlier).
     * @param bool   $once     Whether to execute the listener only once (not yet implemented).
     */
    public function __construct(
        public string $name,
        public int $priority = 10,
        public bool $once = false,
    ) {
    }
}
