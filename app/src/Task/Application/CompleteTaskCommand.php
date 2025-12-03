<?php
declare(strict_types=1);

namespace App\Task\Application;

final readonly class CompleteTaskCommand
{
    public function __construct(
        public string $id
    ) {
    }
}
