<?php
declare(strict_types=1);

namespace App\Task\Domain;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;
    public function get(string $taskId): Task;
}
