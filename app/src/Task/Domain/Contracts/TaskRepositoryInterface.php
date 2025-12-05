<?php

declare(strict_types=1);

namespace App\Task\Domain\Contracts;

use App\Task\Domain\Entity\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;

    public function get(string $taskId): Task;
}
