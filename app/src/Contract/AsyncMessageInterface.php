<?php
declare(strict_types=1);

namespace App\Contract;

interface AsyncMessageInterface
{
    public function getPayload(): string;
}
