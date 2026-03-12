<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_events')]
class TaskEventEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $taskId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $eventName;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $occurredAt;

    public function __construct(
        string $taskId,
        string $eventName,
        array $payload,
        \DateTimeImmutable $occurredAt,
    ) {
        $this->taskId = $taskId;
        $this->eventName = $eventName;
        $this->payload = $payload;
        $this->occurredAt = $occurredAt;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
