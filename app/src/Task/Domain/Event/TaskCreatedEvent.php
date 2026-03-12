<?php

declare(strict_types=1);

namespace App\Task\Domain\Event;

final readonly class TaskCreatedEvent
{
    public function __construct(
        public string $taskId,
        public string $title,
        public ?string $description,
        public ?int $assignedUserId,
        public string $status,
        public \DateTimeImmutable $occurredAt,
    ) {
    }
}
