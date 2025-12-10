<?php

declare(strict_types=1);

namespace App\Task\Application\Messenger\Handler;

use App\Task\Application\Export\TaskExporter;
use App\Task\Application\Messenger\Command\InitiateTaskExportCommand;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class InitiateTaskExportHandler
{
    public function __construct(private TaskExporter $taskExporter)
    {
    }

    public function __invoke(InitiateTaskExportCommand $command): Response
    {
        return $this->taskExporter->export($command->criteria, $command->format);
    }
}
