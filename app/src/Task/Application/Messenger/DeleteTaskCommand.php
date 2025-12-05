<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger;

final readonly class DeleteTaskCommand
{
    public function __construct(
        public string $id,
    ) {
    }
}
