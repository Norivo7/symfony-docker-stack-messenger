<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger;

use App\Task\Domain\Contracts\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CompleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(CompleteTaskCommand $command): void
    {
        $task = $this->taskRepository->get($command->id);

        $task->complete();

        $this->taskRepository->save($task);
    }
}
