<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Queue;

use Closure;

interface QueueDriverInterface
{
    /**
     * Push a job onto the queue.
     *
     * @param string $jobName The name of the job (usually the hook name).
     * @param array  $payload The data to be processed.
     *
     * @return void
     */
    public function push(string $jobName, array $payload): void;
}
