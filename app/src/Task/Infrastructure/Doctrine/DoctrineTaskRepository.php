<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine;

use App\Task\Domain\Task;
use App\Task\Domain\TaskNotFoundException;
use App\Task\Domain\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Task $task): void
    {
        $entity = $this->entityManager->find(TaskEntity::class, $task->getId());

        if (!$entity instanceof TaskEntity) {
            $entity = TaskMapper::toEntity($task);
            $this->entityManager->persist($entity);
        } else {
            TaskMapper::updateEntity($entity, $task);
        }

        $this->entityManager->flush();
    }

    public function get(string $taskId): Task
    {
        $entity = $this->entityManager->find(TaskEntity::class, $taskId);

        if (!$entity instanceof TaskEntity) {
            throw new TaskNotFoundException("Task with ID {$taskId} not found.");
        }

        return TaskMapper::fromEntity($entity);
    }
}
