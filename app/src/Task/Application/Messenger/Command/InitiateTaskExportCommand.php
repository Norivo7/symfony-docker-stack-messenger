<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Command;

use App\Task\Application\Query\Criteria\TaskSearchCriteria;

final readonly class InitiateTaskExportCommand
{
    public function __construct(
        public TaskSearchCriteria $criteria,
        public string $format = 'csv',
    ) {
    }

}
