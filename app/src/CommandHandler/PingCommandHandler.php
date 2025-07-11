<?php
declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\PingCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PingCommandHandler
{
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(PingCommand $message): void
    {
        $this->logger->info('✅ Ping handled: ' . $message->getLogContext()['payload']);    }

}
