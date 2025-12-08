<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\Ping\Application\Command\SendEmailCommand;
use App\User\Application\Query\GetUserByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    use HandleTrait;

    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function fetchUser(int $id): JsonResponse
    {
        $data = $this->handle(new GetUserByIdQuery($id));

        return $this->json($data);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/send-email', name: 'send_email')]
    public function sendEmail(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new SendEmailCommand(
            'szymik.kamil97@gmail.com',
            'Welcome!',
            'John Doe'
        ));

        return new JsonResponse(['status' => 'Message dispatched']);
    }

    #[Route('/send-email-fail', name: 'send_email_fail')]
    public function sendEmailFail(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new SendEmailCommand(
            'thisisnotanemail',
            'Welcome!',
            'John Doe'
        ));

        return new JsonResponse(['status' => 'Message dispatched']);
    }
}
