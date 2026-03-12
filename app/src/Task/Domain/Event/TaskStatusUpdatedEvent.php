<?php

declare(strict_types=1);

namespace App\Task\Domain\Event;

final readonly class TaskStatusUpdatedEvent
{
    public function __construct(
        public string $taskId,
        public string $oldStatus,
        public string $newStatus,
        public \DateTimeImmutable $occurredAt,
    ) {
    }
}
