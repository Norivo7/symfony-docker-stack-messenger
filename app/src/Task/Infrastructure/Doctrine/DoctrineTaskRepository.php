<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine;

use App\Task\Application\Query\Criteria\TaskSearchCriteria;
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

    public function getAll(): array
    {
        $this->entityManager->getRepository(TaskEntity::class);
        $entities = $this->entityManager->getRepository(TaskEntity::class)->findAll();

        return array_map(
            static fn (TaskEntity $entity) => TaskMapper::fromEntity($entity),
            $entities
        );
    }

    public function search(TaskSearchCriteria $criteria): array
    {
        $queryBuilder = $this->entityManager
            ->getRepository(TaskEntity::class)
            ->createQueryBuilder('task')
            ->orderBy('task.createdAt', 'DESC');

        if (null !== $criteria->status) {
            $queryBuilder
                ->andWhere('task.status = :status')
                ->setParameter('status', $criteria->status->value);
        }

        if (null !== $criteria->createdFrom) {
            $queryBuilder
                ->andWhere('task.createdAt >= :createdFrom')
                ->setParameter('createdFrom', $criteria->createdFrom);
        }

        if (null !== $criteria->createdTo) {
            $queryBuilder
                ->andWhere('task.createdAt <= :createdTo')
                ->setParameter('createdTo', $criteria->createdTo);
        }

        $entities = $queryBuilder->getQuery()->getResult();

        return array_map(
            static fn (TaskEntity $entity) => TaskMapper::fromEntity($entity),
            $entities
        );
    }
}
