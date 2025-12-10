<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Messenger\Command\RenameTaskCommand;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RenameTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(RenameTaskCommand $command): void
    {
        $task = $this->taskRepository->get($command->id);

        $task->rename($command->title);

        $this->taskRepository->save($task);
    }
}
