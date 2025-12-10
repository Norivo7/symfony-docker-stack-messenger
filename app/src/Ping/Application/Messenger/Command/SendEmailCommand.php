<?php

declare(strict_types=1);

namespace App\Ping\Application\Messenger\Command;

use App\Shared\Contract\AsyncMessageInterface;
use App\Shared\Traits\ContextLoggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SendEmailCommand implements AsyncMessageInterface
{
    use ContextLoggableTrait;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $to,

        #[Assert\NotBlank]
        public string $subject,

        #[Assert\NotBlank]
        public string $content,
    ) {
    }
}
