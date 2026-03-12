<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Messenger\Command\CreateTaskCommand;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Task\Domain\Factory\TaskFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskFactory $taskFactory,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $task = $this->taskFactory->create(
            $command->id,
            $command->title,
            $command->description,
            $command->assignedUserId,
        );

        $this->taskRepository->save($task);
    }
}
