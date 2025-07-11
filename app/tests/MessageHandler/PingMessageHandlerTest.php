<?php
declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Command\PingCommand;
use App\CommandHandler\PingCommandHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class PingMessageHandlerTest extends TestCase
{
    public function testPing(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $message = new PingCommand('ping test');
        $handler = new PingCommandHandler($logger);

        $this->expectNotToPerformAssertions();

        $handler($message);
    }

    public function testLogsPingPayload(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Ping handled'));

        $message = new PingCommand('ping');
        $handler = new PingCommandHandler($logger);

        $handler($message);
    }


}
