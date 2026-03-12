<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine\Repository;

use App\Task\Infrastructure\Doctrine\Entity\TaskEventEntity;
use App\Task\Infrastructure\Doctrine\Mapper\TaskEventPayloadMapper;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTaskEventRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function append(string $taskId, object $event): void
    {
        $occurredAt = new \DateTimeImmutable();
        if (property_exists($event, 'occurredAt') && $event->occurredAt instanceof \DateTimeImmutable) {
            $occurredAt = $event->occurredAt;
        }

        $entity = new TaskEventEntity(
            taskId: $taskId,
            eventName: $event::class,
            payload: TaskEventPayloadMapper::toPayload($event),
            occurredAt: $occurredAt,
        );

        $this->entityManager->persist($entity);
    }

    public function getByTaskId(string $taskId): array
    {
        return $this->entityManager
            ->getRepository(TaskEventEntity::class)
            ->findBy(['taskId' => $taskId], ['occurredAt' => 'ASC']);
    }
}
