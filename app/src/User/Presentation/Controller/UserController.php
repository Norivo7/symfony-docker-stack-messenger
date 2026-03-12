<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\Ping\Application\Messenger\Command\SendEmailCommand;
use App\User\Application\Messenger\Command\ImportUsersCommand;
use App\User\Application\Messenger\Query\GetUserByIdQuery;
use App\User\Domain\Contracts\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function fetchUser(int $id): JsonResponse
    {
        $data = $this->handle(new GetUserByIdQuery($id));

        return $this->json($data);
    }

    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(): JsonResponse
    {
        $this->messageBus->dispatch(new SendEmailCommand(
            'szymik.kamil97@gmail.com',
            'Welcome!',
            'John Doe'
        ));

        return new JsonResponse(['status' => 'Message dispatched']);
    }

    #[Route('/send-email-fail', name: 'send_email_fail')]
    public function sendEmailFail(): JsonResponse
    {
        $this->messageBus->dispatch(new SendEmailCommand(
            'thisisnotanemail',
            'Welcome!',
            'John Doe'
        ));

        return new JsonResponse(['status' => 'Message dispatched']);
    }

    #[Route('/users/import', name: 'user_import', methods: ['POST'])]
    public function import(): JsonResponse
    {
        $this->messageBus->dispatch(new ImportUsersCommand());

        return new JsonResponse(
            ['message' => 'Users imported successfully.'],
            Response::HTTP_OK
        );
    }

    #[Route('/auth/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $email = $data['email'] ?? null;

        if (!is_string($email) || '' === trim($email)) {
            return new JsonResponse(
                ['error' => 'Email is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->userRepository->findByEmail($email);

        if (null === $user) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            [
                'message' => 'Login successful',
                'userId' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/me', name: 'user_me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        $userId = $request->headers->get('X-User-Id');

        if (null === $userId || !ctype_digit($userId)) {
            return new JsonResponse(
                ['error' => 'X-User-Id header is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->userRepository->findById((int) $userId);

        if (null === $user) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ],
            Response::HTTP_OK
        );
    }
}
