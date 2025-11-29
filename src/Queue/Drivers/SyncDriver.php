<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Queue\Drivers;

use AlizHarb\Hookx\Queue\QueueDriverInterface;

class SyncDriver implements QueueDriverInterface
{
    /**
     * Push a job onto the queue (executes immediately for Sync).
     *
     * @param string               $jobName
     * @param array<string|int, mixed> $payload
     *
     * @return void
     */
    public function push(string $jobName, array $payload): void
    {
        // In a real implementation, this would dispatch the hook immediately.
        // But since this is a driver, it might need a way to call back to the HookManager.
        // For now, we'll just simulate it or leave it as a placeholder for the architecture.
    }
}
