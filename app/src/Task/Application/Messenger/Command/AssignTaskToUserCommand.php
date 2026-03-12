<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Command;

final readonly class AssignTaskToUserCommand
{
    public function __construct(
        public string $taskId,
        public int $userId,
    ) {
    }
}
