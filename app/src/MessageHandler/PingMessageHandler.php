<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PingMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PingMessageHandler
{
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(PingMessage $message): void
    {
        $this->logger->info('✅ Ping handled: ' . $message->getLogContext()['payload']);    }

}
