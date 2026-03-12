<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Messenger\Command\AssignTaskToUserCommand;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AssignTaskToUserHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(AssignTaskToUserCommand $command): void
    {
        $task = $this->taskRepository->get($command->taskId);
        $task->assignToUser($command->userId);

        $this->taskRepository->save($task);
    }
}
