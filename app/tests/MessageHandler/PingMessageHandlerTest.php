<?php
declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Message\PingMessage;
use App\MessageHandler\PingMessageHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class PingMessageHandlerTest extends TestCase
{
    public function testPing(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $message = new PingMessage('ping test');
        $handler = new PingMessageHandler($logger);

        $this->expectNotToPerformAssertions();

        $handler($message);
    }

    public function testLogsPingPayload(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Ping handled'));

        $message = new PingMessage('ping');
        $handler = new PingMessageHandler($logger);

        $handler($message);
    }


}
