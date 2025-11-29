<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Attributes;

use Attribute;

/**
 * Attribute Async
 *
 * Marks a hook listener to be executed asynchronously using Fibers.
 *
 * @package AlizHarb\Hookx\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class Async
{
    /**
     * Create a new Async attribute instance.
     */
    public function __construct() {}
}
