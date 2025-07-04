<?php
declare(strict_types=1);

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\PingMessage;

#[AsMessageHandler]
final class PingMessageHandler
{
    public function __invoke(PingMessage $message): void
    {
        dump('Received message: ' . $message->getPayload());
    }

}
