<?php

namespace App\Scheduler;

use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask(expression: '0 4 * * *', timezone: 'Europe/Berlin')]
class CleanMonitoredMessagesScheduler
{

    public function __construct(
        private readonly MessageBusInterface $bus
    )
    {

    }

    public function __invoke(): void
    {
        $this->bus->dispatch(new RunCommandMessage('messenger:monitor:purge'));
    }
}
