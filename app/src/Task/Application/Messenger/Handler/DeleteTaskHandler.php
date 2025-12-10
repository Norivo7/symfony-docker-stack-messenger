<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Messenger\Command\DeleteTaskCommand;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->get($command->id);

        $task->assertCanBeDeleted();

        $this->taskRepository->delete($task);
    }
}
