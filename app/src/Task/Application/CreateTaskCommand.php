<?php
declare(strict_types=1);

namespace App\Task\Application;

final readonly class CreateTaskCommand
{
    public function __construct(
        public string $title,
    ) {
    }
}
