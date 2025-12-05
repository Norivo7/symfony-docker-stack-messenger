<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger;

use App\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Task\Domain\Entity\Task;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $task = Task::create($command->id, $command->title);

        $this->taskRepository->save($task);
    }
}
