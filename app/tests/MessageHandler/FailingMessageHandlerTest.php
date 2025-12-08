<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Ping\Application\Command\FailingCommand;
use App\Ping\Application\CommandHandler\FailingCommandHandler;
use PHPUnit\Framework\TestCase;

final class FailingMessageHandlerTest extends TestCase
{
    public function testPing(): void
    {
        $message = new FailingCommand();
        $handler = new FailingCommandHandler();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('FailingCommand triggered exception');

        $handler($message);
    }
}
