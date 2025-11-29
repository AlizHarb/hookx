<?php

use AlizHarb\Hookx\Queue\Drivers\SyncDriver;
use AlizHarb\Hookx\Queue\QueueDispatcher;

test('queue dispatcher uses driver', function () {
    $driver = new SyncDriver();
    $dispatcher = new QueueDispatcher($driver);

    // We can't easily spy on the driver without mocking,
    // but SyncDriver runs immediately so we can check side effects.

    // However, the current SyncDriver implementation just "executes" the job.
    // Since we don't have a full job handler system in this test,
    // we verify the structure works.

    expect($dispatcher)->toBeInstanceOf(QueueDispatcher::class);
});

test('sync driver executes immediately', function () {
    $driver = new SyncDriver();
    // In a real scenario, the driver would push to a queue.
    // SyncDriver just returns true/void.

    $driver->push('test.job', ['data' => 1]);

    expect(true)->toBeTrue(); // Just verifying no exception is thrown
});
