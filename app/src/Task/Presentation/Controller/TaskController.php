<?php

declare(strict_types=1);

namespace App\Task\Presentation\Controller;

use App\Task\Application\Messenger\Command\AssignTaskToUserCommand;
use App\Task\Application\Messenger\Command\ChangeTaskStatusCommand;
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
use App\Task\Domain\Exception\TaskNotFoundException;
use App\Task\Infrastructure\Doctrine\Repository\DoctrineTaskEventRepository;
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
        private readonly DoctrineTaskEventRepository $taskEventStore,
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
        $description = $data['description'] ?? null;
        $assignedUserId = $data['assignedUserId'] ?? null;

        if (!is_string($title) || '' === trim($title)) {
            return new JsonResponse(
                ['error' => 'Title is required'],
                Response::HTTP_BAD_REQUEST);
        }

        $title = trim($title);

        // todo: use a proper UUID generator
        $id = uniqid('', true);

        $command = new CreateTaskCommand($id, $title, $description, $assignedUserId);

        // todo: handle message bus exceptions, if transport changes to async
        $this->messageBus->dispatch($command);

        return new JsonResponse(
            [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'assignedUserId' => $assignedUserId,
            ],
            Response::HTTP_CREATED
        );
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
                'description' => $task->getDescription(),
                'assignedUserId' => $task->getAssignedUserId(),
                'status' => $task->getStatus()->value,
                'createdAt' => $task->getCreatedAt()->format(DATE_ATOM),
                'completedAt' => $task->getCompletedAt()?->format(DATE_ATOM),
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/tasks/{id}/status', name: 'task_change_status', methods: ['PATCH'])]
    public function changeStatus(string $id, Request $request): JsonResponse
    {
        $raw = $request->getContent();

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $status = $data['status'] ?? null;

        if (!is_string($status)) {
            return new JsonResponse(
                ['error' => 'Status is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new ChangeTaskStatusCommand($id, $status);

        try {
            $this->messageBus->dispatch($command);
        } catch (\ValueError) {
            return new JsonResponse(
                ['error' => 'Invalid status value'],
                Response::HTTP_BAD_REQUEST
            );
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            if ($previous instanceof TaskNotFoundException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }

        return new JsonResponse(
            ['message' => "Task with ID {$id} status updated."],
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

    #[Route('/tasks/{id}/history', name: 'task_history', methods: ['GET'])]
    public function history(string $id): JsonResponse
    {
        $events = $this->taskEventStore->getByTaskId($id);

        $result = [];

        foreach ($events as $event) {
            $result[] = [
                'eventName' => $event->getEventName(),
                'payload' => $event->getPayload(),
                'occurredAt' => $event->getOccurredAt()->format(DATE_ATOM),
            ];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    #[Route('/tasks/{id}/assign', name: 'task_assign', methods: ['PATCH'])]
    public function assign(string $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $userId = $data['userId'] ?? null;

        if (!is_int($userId)) {
            return new JsonResponse(
                ['error' => 'userId is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->messageBus->dispatch(new AssignTaskToUserCommand($id, $userId));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            if ($previous instanceof TaskNotFoundException) {
                return new JsonResponse(
                    ['error' => $previous->getMessage()],
                    Response::HTTP_NOT_FOUND
                );
            }

            throw $e;
        }

        return new JsonResponse(
            ['message' => 'Task assigned successfully.'],
            Response::HTTP_OK
        );
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
