<?php

declare(strict_types=1);

namespace App\Task\Application\Strategy;

final readonly class TaskStatusStrategyResolver
{
    /**
     * @param iterable<TaskStatusStrategyInterface> $strategies
     */
    public function __construct(
        private iterable $strategies,
    ) {
    }

    public function resolve(string $status): TaskStatusStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($status)) {
                return $strategy;
            }
        }

        throw new \LogicException(sprintf('No strategy found for status "%s".', $status));
    }
}
