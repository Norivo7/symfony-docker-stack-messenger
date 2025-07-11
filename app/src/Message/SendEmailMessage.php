<?php
declare(strict_types=1);

namespace App\Message;

use App\Message\Traits\ContextLoggableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use App\Contract\AsyncMessageInterface;

final readonly class SendEmailMessage implements AsyncMessageInterface
{
    use ContextLoggableTrait;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $to,

        #[Assert\NotBlank]
        public string $subject,

        #[Assert\NotBlank]
        public string $content
    ) {}

}
