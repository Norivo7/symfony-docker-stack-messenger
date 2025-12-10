<?php

declare(strict_types=1);

namespace App\Task\Application\Query\Criteria;

use App\Task\Domain\Enums\TaskStatus;

final readonly class TaskSearchCriteria
{
    public function __construct(
        public ?TaskStatus $status = null,
        public ?\DateTimeImmutable $createdFrom = null,
        public ?\DateTimeImmutable $createdTo = null,
    ) {
    }
}
