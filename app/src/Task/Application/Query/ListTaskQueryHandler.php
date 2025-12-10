<?php

declare(strict_types=1);

namespace App\Task\Application\Query;

use App\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Task\Domain\Entity\Task;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListTaskQueryHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * @return array<Task>
     */
    public function __invoke(ListTaskQuery $query): array
    {
        return $this->taskRepository->search($query->criteria);
    }
}
