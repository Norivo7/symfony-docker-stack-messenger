<?php
declare(strict_types=1);

namespace App\Messenger\Middleware;

use Cassandra\Exception\ValidationException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private ValidatorInterface $validator) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $violations = $this->validator->validate($envelope->getMessage());

        if (count($violations) > 0) {
            throw new ValidationFailedException($envelope->getMessage(), $violations);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
