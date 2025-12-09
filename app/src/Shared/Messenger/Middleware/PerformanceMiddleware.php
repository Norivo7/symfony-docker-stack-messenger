<?php

declare(strict_types=1);

namespace App\Shared\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final readonly class PerformanceMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $messageClass = get_class($envelope->getMessage());

        $start = microtime(true);
        $result = $stack->next()->handle($envelope, $stack);

        $duration = round((microtime(true) - $start) * 1000, 2);
        $this->logger->info('Handled message', [
            'message_class' => $messageClass,
            'duration_ms' => $duration,
        ]);

        return $result;
    }
}
