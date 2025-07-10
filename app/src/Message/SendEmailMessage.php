<?php
declare(strict_types=1);

namespace App\Message;

use App\Contract\AsyncMessageInterface;

final readonly class SendEmailMessage implements AsyncMessageInterface
{

    public function __construct(
        public string $to,
        public string $subject,
        public string $content
    ) {}

    public function getLogContext(): array
    {
        return [
            'to' =>$this->to,
            'subject' =>$this->subject,
            'content' =>$this->content
            ];
    }
}
