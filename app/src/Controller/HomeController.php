<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\PingMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    public function testMessenger(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new PingMessage('Hello World!'));
        return new JsonResponse(['status' => 'Message dispatched']);
    }

}
