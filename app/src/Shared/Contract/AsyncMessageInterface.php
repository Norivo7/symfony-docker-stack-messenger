<?php

declare(strict_types=1);

namespace App\Shared\Contract;

interface AsyncMessageInterface
{
    public function getLogContext(): array;
}
