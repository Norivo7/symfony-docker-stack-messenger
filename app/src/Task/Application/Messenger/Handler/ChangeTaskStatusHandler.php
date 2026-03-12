<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Messenger\Command\ChangeTaskStatusCommand;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Task\Domain\Enums\TaskStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ChangeTaskStatusHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(ChangeTaskStatusCommand $command): void
    {
        $task = $this->taskRepository->get($command->id);

        $task->changeStatus(TaskStatus::from($command->status));

        $this->taskRepository->save($task);
    }
}
