<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Queue\Drivers;

use AlizHarb\Hookx\Queue\QueueDriverInterface;

class RedisDriver implements QueueDriverInterface
{
    public function __construct(
        private \Redis $redis,
        private string $queueName = 'hookx_queue'
    ) {}

    /**
     * Push a job onto the queue.
     *
     * @param string               $jobName
     * @param array<string|int, mixed> $payload
     * @return void
     */
    public function push(string $jobName, array $payload): void
    {
        $data = json_encode([
            'job' => $jobName,
            'payload' => $payload,
            'timestamp' => time(),
        ]);

        $this->redis->rPush($this->queueName, $data);
    }
}
