<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine;

use App\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\TaskNotFoundException;
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

    public function delete(Task $task): void
    {
        // maybe-todo: get the entity reference instead of find
        $entity = $this->entityManager->find(TaskEntity::class, $task->getId());

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
