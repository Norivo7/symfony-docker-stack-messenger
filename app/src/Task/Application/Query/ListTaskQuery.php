<?php

declare(strict_types=1);

namespace App\Task\Application\Query;

use App\Task\Application\Query\Criteria\TaskSearchCriteria;

final readonly class ListTaskQuery
{
    public function __construct(
        public TaskSearchCriteria $criteria,
    ) {
    }
}
