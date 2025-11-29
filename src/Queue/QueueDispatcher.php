<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Queue;

class QueueDispatcher
{
    public function __construct(
        private QueueDriverInterface $driver
    ) {}

    /**
     * Dispatch a job to the queue.
     *
     * @param string               $hookName
     * @param array<string|int, mixed> $payload
     * @return void
     */
    public function dispatch(string $hookName, array $payload): void
    {
        $this->driver->push($hookName, $payload);
    }
}
