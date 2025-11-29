<?php

require __DIR__ . '/../vendor/autoload.php';

use AlizHarb\Hookx\Queue\QueueDispatcher;
use AlizHarb\Hookx\Queue\Drivers\SyncDriver;

// In a real app, you would use RedisDriver
// $redis = new \Redis(); $redis->connect('127.0.0.1');
// $driver = new RedisDriver($redis);

// For this example, we use SyncDriver which runs immediately
$driver = new SyncDriver();
$dispatcher = new QueueDispatcher($driver);

echo "--- Background Hooks (Sync Driver) ---\n";

// Dispatching to the queue
// The driver is responsible for pushing this to a storage (Redis/DB)
// And a worker would pick it up.
$dispatcher->dispatch('email.send', [
    'to' => 'user@example.com',
    'subject' => 'Hello Background World'
]);

echo "Dispatched 'email.send' to queue.\n";
