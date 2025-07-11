<?php
declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Message\FailingMessage;
use App\MessageHandler\FailingMessageHandler;
use PHPUnit\Framework\TestCase;

final class FailingMessageHandlerTest extends TestCase
{
    public function testPing(): void
    {
        $message = new FailingMessage();
        $handler = new FailingMessageHandler();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('FailingMessage triggered exception');

        $handler($message);
    }

}
