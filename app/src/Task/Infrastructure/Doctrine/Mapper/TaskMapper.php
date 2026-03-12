<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine\Mapper;

use App\Task\Domain\Entity\Task;
use App\Task\Infrastructure\Doctrine\Entity\TaskEntity;

final class TaskMapper
{
    public static function toEntity(Task $task): TaskEntity
    {
        return new TaskEntity(
            id: $task->getId(),
            title: $task->getTitle(),
            description: $task->getDescription(),
            assignedUserId: $task->getAssignedUserId(),
            status: $task->getStatus(),
            createdAt: $task->getCreatedAt(),
            completedAt: $task->getCompletedAt(),
        );
    }

    public static function fromEntity(TaskEntity $taskEntity): Task
    {
        return Task::rebuild(
            id: $taskEntity->getId(),
            title: $taskEntity->getTitle(),
            status: $taskEntity->getStatus(),
            description: $taskEntity->getDescription(),
            assignedUserId: $taskEntity->getAssignedUserId(),
            createdAt: $taskEntity->getCreatedAt(),
            completedAt: $taskEntity->getCompletedAt(),
        );
    }

    public static function updateEntity(TaskEntity $entity, Task $task): void
    {
        $entity->setTitle($task->getTitle());
        $entity->setStatus($task->getStatus());
        $entity->setCreatedAt($task->getCreatedAt());
        $entity->setCompletedAt($task->getCompletedAt());
    }
}
