<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Attributes;

use Attribute;

/**
 * Attribute Background
 *
 * Marks a hook listener to be executed in the background via a queue.
 *
 * @package AlizHarb\Hookx\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class Background
{
    /**
     * @param string|null $queue      The name of the queue to push to.
     * @param string|null $connection The name of the queue connection.
     */
    public function __construct(
        public ?string $queue = null,
        public ?string $connection = null,
    ) {
    }
}
