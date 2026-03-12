<?php

declare(strict_types=1);

namespace App\Task\Application\View;

use App\Task\Domain\Entity\Task;

final readonly class TaskSerializer
{
    public static function serialize(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'assignedUserId' => $task->getAssignedUserId(),
            'status' => $task->getStatus()->value,
            'createdAt' => $task->getCreatedAt()->format(DATE_ATOM),
            'completedAt' => $task->getCompletedAt()?->format(DATE_ATOM),
        ];
    }

    /**
     * @param array<Task> $tasks
     */
    public function serializeList(array $tasks): array
    {
        return array_map(
            static fn (Task $task) => self::serialize($task),
            $tasks
        );
    }
}
