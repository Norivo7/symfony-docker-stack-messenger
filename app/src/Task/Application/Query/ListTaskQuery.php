<?php

declare(strict_types=1);

namespace App\Task\Application\Query;

use App\Task\Domain\Enums\TaskStatus;

final readonly class ListTaskQuery
{
    public function __construct(public ?TaskStatus $status = null)
    {
    }
}
