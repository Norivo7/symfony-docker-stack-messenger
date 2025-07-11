<?php
declare(strict_types=1);

namespace App\Message;

use App\Contract\AsyncMessageInterface;
use App\Message\Traits\ContextLoggableTrait;

final readonly class FailingMessage implements AsyncMessageInterface
{
    use ContextLoggableTrait;

    public function __construct(private string $payload = 'fail') {}
}
