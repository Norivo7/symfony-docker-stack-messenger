<?php

declare(strict_types=1);

namespace App\Task\Application\Export;

use App\Task\Application\Query\Criteria\TaskSearchCriteria;
use Symfony\Component\HttpFoundation\Response;

final readonly class TaskExporter
{
    public function export(TaskSearchCriteria $criteria, string $format): Response
    {
        return new Response();
    }
}
