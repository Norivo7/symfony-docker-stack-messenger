<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Messenger\Command\ChangeTaskStatusCommand;
use App\Task\Application\Strategy\TaskStatusStrategyResolver;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ChangeTaskStatusHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskStatusStrategyResolver $strategyResolver,
    ) {
    }

    public function __invoke(ChangeTaskStatusCommand $command): void
    {
        $task = $this->taskRepository->get($command->id);

        $strategy = $this->strategyResolver->resolve($command->status);
        $strategy->apply($task);

        $this->taskRepository->save($task);
    }
}
