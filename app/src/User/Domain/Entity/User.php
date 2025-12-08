<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
    ) {
    }
}
