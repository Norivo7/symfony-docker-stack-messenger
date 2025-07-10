<?php
declare(strict_types=1);

namespace App\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final readonly class ErrorLoggingMiddleware implements MiddlewareInterface
{
    // todo: separate performance metrics | single responsibility principle
    public function __construct(private LoggerInterface $logger) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $messageClass = get_class($envelope->getMessage());
        $start = microtime(true);

        try {
            $result = $stack->next()->handle($envelope, $stack);
        } catch (\Throwable $throwable) {
            $this->logger->error('Messenger error: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'message_class' => $messageClass
            ]);
            throw $throwable;
        }

        $duration = round((microtime(true) - $start) * 1000, 2);
        $this->logger->info(" Handled $messageClass in {$duration}ms");

        return $result;
    }
}
