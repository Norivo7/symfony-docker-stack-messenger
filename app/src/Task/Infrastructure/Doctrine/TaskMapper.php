<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine;

use App\Task\Domain\Task;

final class TaskMapper
{
    public static function toEntity(Task $task): TaskEntity
    {
        return new TaskEntity(
            id: $task->getId(),
            title: $task->getTitle(),
            status: $task->getStatus(),
            createdAt: $task->getCreatedAt(),
            completedAt: $task->getCompletedAt(),
        );
    }

    public static function toDomain(TaskEntity $taskEntity): Task
    {
        return Task::rebuild(
            id: $taskEntity->getId(),
            title: $taskEntity->getTitle(),
            status: $taskEntity->getStatus(),
            createdAt: $taskEntity->getCreatedAt(),
            completedAt: $taskEntity->getCompletedAt(),
        );
    }
}
