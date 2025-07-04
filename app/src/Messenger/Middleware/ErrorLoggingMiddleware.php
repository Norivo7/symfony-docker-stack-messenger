<?php
declare(strict_types=1);

namespace App\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class ErrorLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (ExceptionInterface $exception) {
            $this->logger->error('Messenger error: ' . $exception->getMessage(), [
                'exception' => $exception,
                'message_class' => get_class($envelope->getMessage())
            ]);
            throw $exception;
        }
    }
}
