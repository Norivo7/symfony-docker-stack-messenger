<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Doctrine\Entity;

use App\Task\Domain\Enums\TaskStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
class TaskEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $assignedUserId;

    #[ORM\Column(enumType: TaskStatus::class)]
    private TaskStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt;

    public function __construct(
        string $id,
        string $title,
        ?string $description,
        ?int $assignedUserId,
        TaskStatus $status,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $completedAt,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->assignedUserId = $assignedUserId;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->completedAt = $completedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getAssignedUserId(): ?int
    {
        return $this->assignedUserId;
    }

    public function setAssignedUserId(?int $assignedUserId): void
    {
        $this->assignedUserId = $assignedUserId;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }
}
