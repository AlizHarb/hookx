<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Queue;

class QueueDispatcher
{
    public function __construct(
        private QueueDriverInterface $driver
    ) {}

    public function dispatch(string $hookName, array $payload): void
    {
        $this->driver->push($hookName, $payload);
    }
}
