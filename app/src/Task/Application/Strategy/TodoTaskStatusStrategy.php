<?php

declare(strict_types=1);

namespace App\Task\Application\Strategy;

use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enums\TaskStatus;

final class TodoTaskStatusStrategy implements TaskStatusStrategyInterface
{
    public function supports(string $status): bool
    {
        return TaskStatus::TODO->value === $status;
    }

    public function apply(Task $task): void
    {
        $task->changeStatus(TaskStatus::TODO);
    }
}
