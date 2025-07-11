<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\FailingMessage;
use App\Message\PingMessage;
use App\Message\SendEmailMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    public function messengerPing(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new PingMessage('Hello World!'));
        return new JsonResponse(['status' => 'Message dispatched']);
    }

    // docker compose exec php php bin/console messenger:consume async -vv
    #[Route('/delayed-ping', name: 'delayed_ping', methods: ['GET'])]
    public function messengerDelayedPing(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new PingMessage('Hello World!'),
            [new DelayStamp(5000)]);
        return new JsonResponse(['status' => 'Message dispatched']);
    }

    #[Route('/failed-ping', name: 'failed_ping', methods: ['GET'])]
    public function messengerFail(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new FailingMessage());
        return new JsonResponse(['status' => 'Message dispatched']);
    }

    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new SendEmailMessage(
            'szymik.kamil97@gmail.com',
            'Welcome!',
            'John Doe'
        ));

        return new JsonResponse(['status' => 'Message dispatched']);
    }

    #[Route('/send-email-fail', name: 'send_email_fail')]
    public function sendEmailFail(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new SendEmailMessage(
            'thisisnotanemail',
            'Welcome!',
            'John Doe'
        ));

        return new JsonResponse(['status' => 'Message dispatched']);
    }


}
