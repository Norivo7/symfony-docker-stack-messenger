<?php
declare(strict_types=1);

namespace App\Task\Domain;

final class Task
{
    private function __construct(
        private string $id,
        private string $title,
        private TaskStatus $status,
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $completedAt,
    ) {
    }

    public static function create(string $id, string $title): self
    {
        if (empty($title) || trim($title) === '') {
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
        ?\DateTimeImmutable $completedAt
    ): self
    {
        return new self(
            id: $id,
            title: $title,
            status: TaskStatus::PENDING,
            createdAt: new \DateTimeImmutable(),
            completedAt: null,
        );
    }

    public function complete(): void
    {
        if ($this->status !== TaskStatus::PENDING) {
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
