<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final readonly class SendEmailMessageHandler
{
    public function __construct(private MailerInterface $mailer) {}

    public function __invoke(SendEmailMessage $message): void
    {
        $email = (new Email())
            ->from('no-reply@john.doe')
            ->to($message->to)
            ->subject($message->subject)
            ->text($message->content);

        $this->mailer->send($email);
    }

}
