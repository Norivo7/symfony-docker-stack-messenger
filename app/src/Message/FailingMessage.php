<?php
declare(strict_types=1);

namespace App\Message;

use App\Contract\AsyncMessageInterface;

final class FailingMessage implements AsyncMessageInterface
{

    public function __construct(private string $payload = 'fail') {}

    public function getPayload(): string
    {
        return $this->payload;
    }
}
