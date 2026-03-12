<?php

declare(strict_types=1);

namespace App\Task\Application\Strategy;

use App\Task\Domain\Entity\Task;

interface TaskStatusStrategyInterface
{
    public function supports(string $status): bool;

    public function apply(Task $task): void;
}
