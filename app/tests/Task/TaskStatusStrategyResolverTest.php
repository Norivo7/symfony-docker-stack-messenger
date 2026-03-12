<?php

declare(strict_types=1);

namespace App\Tests\Task;

use App\Task\Application\Strategy\DoneTaskStatusStrategy;
use App\Task\Application\Strategy\InProgressTaskStatusStrategy;
use App\Task\Application\Strategy\TaskStatusStrategyResolver;
use App\Task\Application\Strategy\TodoTaskStatusStrategy;
use PHPUnit\Framework\TestCase;

final class TaskStatusStrategyResolverTest extends TestCase
{
    public function testItResolvesInProgressStrategy(): void
    {
        $resolver = new TaskStatusStrategyResolver([
            new TodoTaskStatusStrategy(),
            new InProgressTaskStatusStrategy(),
            new DoneTaskStatusStrategy(),
        ]);

        $strategy = $resolver->resolve('in_progress');

        self::assertInstanceOf(InProgressTaskStatusStrategy::class, $strategy);
    }

    public function testItThrowsExceptionForUnsupportedStatus(): void
    {
        $resolver = new TaskStatusStrategyResolver([
            new TodoTaskStatusStrategy(),
            new InProgressTaskStatusStrategy(),
            new DoneTaskStatusStrategy(),
        ]);

        $this->expectException(\LogicException::class);

        $resolver->resolve('unknown_status');
    }
}
