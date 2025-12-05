<?php

declare(strict_types=1);

namespace App\Task\Presentation;

use App\Task\Application\CompleteTaskCommand;
use App\Task\Application\CreateTaskCommand;
use App\Task\Application\RenameTaskCommand;
use App\Task\Domain\TaskNotFoundException;
use App\Task\Domain\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final readonly class TaskController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    #[Route('/tasks', name: 'task_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $raw = $request->getContent();

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST);
        }

        $title = $data['title'] ?? null;

        if (!is_string($title) || '' === trim($title)) {
            return new JsonResponse(
                ['error' => 'Title is required'],
                Response::HTTP_BAD_REQUEST);
        }

        // todo: use a proper UUID generator
        $id = uniqid('', true);

        $command = new CreateTaskCommand($id, $title);

        // todo: handle message bus exceptions, if transport changes to async
        $this->messageBus->dispatch($command);

        return new JsonResponse(
            [
                'id' => $id,
                'title' => $title,
            ],
            Response::HTTP_CREATED);
    }

    #[Route('/tasks/{id}', name: 'task_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        try {
            $task = $this->taskRepository->get($id);
        } catch (TaskNotFoundException) {
            return new JsonResponse(
                ['error' => "Task with ID {$id} not found."],
                Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'status' => $task->getStatus(),
                'createdAt' => $task->getCreatedAt()->format(DATE_ATOM),
                'completedAt' => $task->getCompletedAt()?->format(DATE_ATOM),
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/tasks/{id}/complete', name: 'task_complete', methods: ['PATCH'])]
    public function complete(string $id): JsonResponse
    {
        $command = new CompleteTaskCommand($id);

        try {
            $this->messageBus->dispatch($command);
        } catch (\LogicException $e) { // todo: create domain exception
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_CONFLICT
            );
        }

        return new JsonResponse(
            ['message' => "Task with ID {$id} marked as completed."],
            Response::HTTP_OK);
    }

    #[Route('/tasks/{id}', name: 'task_rename', methods: ['PATCH'])]
    public function rename(string $id, Request $request): JsonResponse
    {
        $raw = $request->getContent();
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST);
        }

        $newTitle = $data['title'] ?? null;

        if (!is_string($newTitle) || '' === trim($newTitle)) {
            return new JsonResponse(
                ['error' => 'Title is required'],
                Response::HTTP_BAD_REQUEST);
        }

        $command = new RenameTaskCommand($id, $newTitle);

        try {
            $this->messageBus->dispatch($command);
        } catch (\DomainException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            ['message' => "Task with ID $id renamed to '$newTitle'."],
            Response::HTTP_OK);
    }
}
