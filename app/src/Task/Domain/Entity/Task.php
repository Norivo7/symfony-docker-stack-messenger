<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\Task\Domain\Enums\TaskStatus;
use App\Task\Domain\Event\TaskCreatedEvent;
use App\Task\Domain\Event\TaskStatusUpdatedEvent;
use App\Task\Domain\Exception\CannotDeleteCompletedTaskException;
use App\Task\Domain\Exception\InvalidTaskTitleException;

final class Task
{
    private function __construct(
        private readonly string $id,
        private string $title,
        private ?string $description,
        private ?int $assignedUserId,
        private TaskStatus $status,
        private readonly \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $completedAt,
        private array $recordedEvents = [],
    ) {
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAssignedUserId(): ?int
    {
        return $this->assignedUserId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public static function create(
        string $id,
        string $title,
        ?string $description,
        ?int $assignedUserId,
    ): self {
        if (empty($title) || '' === trim($title)) {
            throw new InvalidTaskTitleException('Title cannot be empty.');
        }

        $task = new self(
            id: $id,
            title: $title,
            description: $description,
            assignedUserId: $assignedUserId,
            status: TaskStatus::TODO,
            createdAt: new \DateTimeImmutable(),
            completedAt: null,
        );

        $task->recordEvent(new TaskCreatedEvent(
            taskId: $task->id,
            title: $task->title,
            description: $task->description,
            assignedUserId: $task->assignedUserId,
            status: $task->status->value,
            occurredAt: new \DateTimeImmutable(),
        ));

        return $task;
    }

    public static function rebuild(
        string $id,
        string $title,
        TaskStatus $status,
        ?string $description,
        ?int $assignedUserId,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $completedAt,
    ): self {
        return new self(
            id: $id,
            title: $title,
            description: $description,
            assignedUserId: $assignedUserId,
            status: $status,
            createdAt: $createdAt,
            completedAt: $completedAt,
        );
    }

    public function changeStatus(TaskStatus $newStatus): void
    {
        if ($this->status === $newStatus) {
            return;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;

        $this->completedAt = match ($newStatus) {
            TaskStatus::DONE => new \DateTimeImmutable(),
            default => null,
        };

        $this->recordEvent(new TaskStatusUpdatedEvent(
            taskId: $this->id,
            oldStatus: $oldStatus->value,
            newStatus: $newStatus->value,
            occurredAt: new \DateTimeImmutable(),
        ));
    }

    public function assignToUser(int $userId): void
    {
        $this->assignedUserId = $userId;
    }

    public function rename(string $title): void
    {
        $newTitle = trim($title);
        if ('' === $newTitle) {
            throw new InvalidTaskTitleException('Title cannot be empty.');
        }

        $this->title = $newTitle;
    }

    private function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public function assertCanBeDeleted(): void
    {
        if (TaskStatus::DONE === $this->status) {
            throw new CannotDeleteCompletedTaskException('Completed task cannot be deleted.');
        }
    }
}
