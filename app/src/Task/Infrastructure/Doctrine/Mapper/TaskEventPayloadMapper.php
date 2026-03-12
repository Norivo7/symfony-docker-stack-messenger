<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine\Mapper;

use App\Task\Domain\Event\TaskCreatedEvent;
use App\Task\Domain\Event\TaskStatusUpdatedEvent;

final readonly class TaskEventPayloadMapper
{
    public static function toPayload(object $event): array
    {
        return match (true) {
            $event instanceof TaskCreatedEvent => [
                'taskId' => $event->taskId,
                'title' => $event->title,
                'description' => $event->description,
                'assignedUserId' => $event->assignedUserId,
                'status' => $event->status,
                'occurredAt' => $event->occurredAt->format(DATE_ATOM),
            ],
            $event instanceof TaskStatusUpdatedEvent => [
                'taskId' => $event->taskId,
                'oldStatus' => $event->oldStatus,
                'newStatus' => $event->newStatus,
                'occurredAt' => $event->occurredAt->format(DATE_ATOM),
            ],
            default => throw new \LogicException(sprintf('Unsupported event "%s"', $event::class)),
        };
    }
}
