<?php

declare(strict_types=1);

namespace App\Task\Domain;

final class Task
{
    private function __construct(
        private readonly string $id,
        private readonly string $title,
        private TaskStatus $status,
        private readonly \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $completedAt,
    ) {
    }

    public static function create(string $id, string $title): self
    {
        if (empty($title) || '' === trim($title)) {
            throw new \RuntimeException('Title cannot be empty');
        }

        return new self(
            id: $id,
            title: $title,
            status: TaskStatus::PENDING,
            createdAt: new \DateTimeImmutable(),
            completedAt: null,
        );
    }

    public static function rebuild(
        string $id,
        string $title,
        TaskStatus $status,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $completedAt,
    ): self {
        return new self(
            id: $id,
            title: $title,
            status: $status,
            createdAt: $createdAt,
            completedAt: $completedAt,
        );
    }

    public function complete(): void
    {
        if (TaskStatus::PENDING !== $this->status) {
            throw new \RuntimeException('Only pending tasks can be completed.');
        }

        $this->status = TaskStatus::DONE;
        $this->completedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }
}
