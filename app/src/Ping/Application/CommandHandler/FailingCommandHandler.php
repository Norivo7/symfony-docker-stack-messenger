<?php

declare(strict_types=1);

namespace App\Ping\Application\CommandHandler;

use App\Ping\Application\Command\FailingCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FailingCommandHandler
{
    public function __invoke(FailingCommand $message): void
    {
        throw new \RuntimeException('FailingCommand triggered exception');
    }
}
