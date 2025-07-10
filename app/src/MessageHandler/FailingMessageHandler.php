<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\FailingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FailingMessageHandler
{
    public function __invoke(FailingMessage $message): void
    {
        throw new \RuntimeException('FailingMessage triggered exception');
    }
}
