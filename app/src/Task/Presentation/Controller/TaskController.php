<?php

declare(strict_types=1);

namespace App\Task\Presentation\Controller;

use App\Task\Application\Messenger\Command\CompleteTaskCommand;
use App\Task\Application\Messenger\Command\CreateTaskCommand;
use App\Task\Application\Messenger\Command\DeleteTaskCommand;
use App\Task\Application\Messenger\Command\InitiateTaskExportCommand;
use App\Task\Application\Messenger\Command\RenameTaskCommand;
use App\Task\Application\Query\ListTaskQuery;
use App\Task\Application\View\TaskSerializer;
use App\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Exception\CannotDeleteCompletedTaskException;
use App\Task\Domain\Exception\InvalidTaskTitleException;
use App\Task\Domain\Exception\TaskAlreadyCompletedException;
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Presentation\Http\Exception\InvalidTaskFilterException;
use App\Task\Presentation\Http\TaskSearchCriteriaFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly TaskSearchCriteriaFactory $criteriaFactory,
        private readonly TaskSerializer $taskSerializer,
    ) {
    }

    #[Route('/tasks', name: 'task_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            $criteria = $this->criteriaFactory->createFromRequest($request);
        } catch (InvalidTaskFilterException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
        $query = new ListTaskQuery($criteria);

        /** @var array<Task> $tasks */
        $tasks = $this->handle($query);

        $result = $this->taskSerializer->serializeList($tasks);

        return new JsonResponse(
            $result,
            Response::HTTP_OK
        );
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
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            if ($previous instanceof TaskNotFoundException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_NOT_FOUND);
            }

            if ($previous instanceof TaskAlreadyCompletedException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_CONFLICT
                );
            }

            throw $e;
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
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            if ($previous instanceof InvalidTaskTitleException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_BAD_REQUEST);
            }
            if ($previous instanceof TaskNotFoundException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }

        return new JsonResponse(
            ['message' => "Task with ID $id renamed to '$newTitle'."],
            Response::HTTP_OK);
    }

    #[Route('/tasks/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $command = new DeleteTaskCommand($id);

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            if ($previous instanceof CannotDeleteCompletedTaskException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_CONFLICT
                );
            }

            if ($previous instanceof TaskNotFoundException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_NOT_FOUND
                );
            }

            throw $e;
        }

        return new JsonResponse(
            ['message' => null],
            Response::HTTP_NO_CONTENT);
    }

    #[Route('/tasks/export', name: 'task_export', methods: ['POST'])]
    public function export(Request $request, string $format = 'csv'): Response
    {
        $criteria = $this->criteriaFactory->createFromRequest($request);

        try {
            $result = $this->handle(new InitiateTaskExportCommand($criteria, $format));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            throw $e;
        }

        return $result;
    }
}
