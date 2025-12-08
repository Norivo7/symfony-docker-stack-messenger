<?php

declare(strict_types=1);

namespace App\Ping\Application\Command;

use App\Shared\Contract\AsyncMessageInterface;
use App\Shared\Traits\ContextLoggableTrait;

final readonly class PingCommand implements AsyncMessageInterface
{
    use ContextLoggableTrait;

    public function __construct(private string $payload = 'Ping.')
    {
    }
}
