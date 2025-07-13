<?php
declare(strict_types=1);

namespace App\Controller;

use App\Query\GetUserByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function fetchUser(int $id): JsonResponse
    {
        $data = $this->handle(new GetUserByIdQuery($id));
        return $this->json($data);
    }
}
