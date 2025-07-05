<?php
declare(strict_types=1);

namespace App\Message;

use App\Contract\AsyncMessageInterface;

final readonly class PingMessage implements AsyncMessageInterface
{
    private string $payload;

    public function __construct(string $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

}
