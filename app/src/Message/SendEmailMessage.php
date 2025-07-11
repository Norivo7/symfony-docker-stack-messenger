<?php
declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;
use App\Contract\AsyncMessageInterface;

final readonly class SendEmailMessage implements AsyncMessageInterface
{

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $to,

        #[Assert\NotBlank]
        public string $subject,

        #[Assert\NotBlank]
        public string $content
    ) {}

    public function getLogContext(): array
    {
        return [
            'to' => $this->to,
            'subject' => $this->subject,
            'content' => $this->content
            ];
    }
}
