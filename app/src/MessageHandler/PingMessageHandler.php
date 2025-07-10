<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PingMessageHandler
{
    public function __invoke(PingMessage $message): void
    {
        dump('✅ Ping handled: ' . $message->getLogContext());
    }

}
