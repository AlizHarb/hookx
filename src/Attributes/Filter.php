<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Attributes;

use Attribute;

/**
 * Attribute Filter
 *
 * Marks a method as a filter.
 *
 * @package AlizHarb\Hookx\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Filter
{
    /**
     * @param string $name     The name of the filter to apply to.
     * @param int    $priority The execution priority (lower runs earlier).
     */
    public function __construct(
        public string $name,
        public int $priority = 10,
    ) {}
}
