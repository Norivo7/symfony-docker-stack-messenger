<?php

declare(strict_types=1);

namespace App\Task\Presentation;

use App\Task\Application\CreateTaskCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController
{
    public function __construct(
        private MessageBusInterface $messageBus,
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
                ['error' => ('Invalid JSON')],
                Response::HTTP_BAD_REQUEST);
        }

        $title = $data['title'] ?? null;

        if (!is_string($title) || trim($title) === '') {
            return new JsonResponse(
                ['error' => ('Title is required')],
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
                'title' => $title
            ],
            Response::HTTP_CREATED);
    }
}
