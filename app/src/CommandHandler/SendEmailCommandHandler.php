<?php
declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\SendEmailCommand;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final readonly class SendEmailCommandHandler
{
    public function __construct(private MailerInterface $mailer) {}

    public function __invoke(SendEmailCommand $message): void
    {
        $email = (new Email())
            ->from('no-reply@john.doe')
            ->to($message->to)
            ->subject($message->subject)
            ->text($message->content);

        $this->mailer->send($email);
    }

}
