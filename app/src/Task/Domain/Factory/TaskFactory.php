<?php

declare(strict_types=1);

namespace App\Task\Domain\Factory;

use App\Task\Domain\Entity\Task;

final readonly class TaskFactory
{
    public function create(
        string $id,
        string $title,
        ?string $description,
        ?int $assignedUserId,
    ): Task {
        return Task::create(
            id: $id,
            title: $title,
            description: $description,
            assignedUserId: $assignedUserId,
        );
    }
}
