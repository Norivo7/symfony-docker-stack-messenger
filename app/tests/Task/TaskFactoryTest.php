<?php

declare(strict_types=1);

namespace App\Tests\Task;

use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enums\TaskStatus;
use App\Task\Domain\Exception\InvalidTaskTitleException;
use App\Task\Domain\Factory\TaskFactory;
use PHPUnit\Framework\TestCase;

final class TaskFactoryTest extends TestCase
{
    public function testItCreatesTaskWithExpectedData(): void
    {
        $factory = new TaskFactory();

        $task = $factory->create(
            id: 'task-1',
            title: 'Prepare recruitment task',
            description: 'Finish implementation',
            assignedUserId: 1,
        );

        self::assertInstanceOf(Task::class, $task);
        self::assertSame('task-1', $task->getId());
        self::assertSame('Prepare recruitment task', $task->getTitle());
        self::assertSame('Finish implementation', $task->getDescription());
        self::assertSame(1, $task->getAssignedUserId());
        self::assertSame(TaskStatus::TODO, $task->getStatus());
        self::assertNull($task->getCompletedAt());
    }

    public function testItCreatesUnassignedTask(): void
    {
        $factory = new TaskFactory();

        $task = $factory->create(
            id: 'task-2',
            title: 'Unassigned task',
            description: null,
            assignedUserId: null,
        );

        self::assertSame('task-2', $task->getId());
        self::assertSame('Unassigned task', $task->getTitle());
        self::assertNull($task->getDescription());
        self::assertNull($task->getAssignedUserId());
        self::assertSame(TaskStatus::TODO, $task->getStatus());
    }

    public function testItThrowsExceptionForEmptyTitle(): void
    {
        $factory = new TaskFactory();

        $this->expectException(InvalidTaskTitleException::class);

        $factory->create(
            id: 'task-3',
            title: '   ',
            description: null,
            assignedUserId: null,
        );
    }
}
