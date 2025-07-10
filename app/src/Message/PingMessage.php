<?php
declare(strict_types=1);

namespace App\Message;

use App\Contract\AsyncMessageInterface;

final readonly class PingMessage implements AsyncMessageInterface
{

    public function __construct(private string $payload = 'Ping.') {}

    public function getLogContext(): array
    {
        return ['payload' => $this->payload];
    }

}
