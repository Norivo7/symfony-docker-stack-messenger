<?php
declare(strict_types=1);

namespace App\Contract;

interface AsyncMessageInterface
{
    public function getLogContext(): array;
}
