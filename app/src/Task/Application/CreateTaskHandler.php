<?php
declare(strict_types=1);

namespace App\Task\Application;

use App\Task\Domain\Task;
use App\Task\Domain\TaskRepositoryInterface;
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
        // todo: use a better id generator
        $id = bin2hex(random_bytes(16));

        $task = Task::create($id, $command->title);

        $this->taskRepository->save($task);
    }

}
