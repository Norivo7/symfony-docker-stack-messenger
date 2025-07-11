<?php
declare(strict_types=1);

namespace App\Command;

use App\Command\Traits\ContextLoggableTrait;
use App\Contract\AsyncMessageInterface;

final readonly class PingCommand implements AsyncMessageInterface
{
    use ContextLoggableTrait;

    public function __construct(private string $payload = 'Ping.') {}

}
