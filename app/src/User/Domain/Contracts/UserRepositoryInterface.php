<?php

declare(strict_types=1);

namespace App\User\Domain\Contracts;

use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function flush(): void;
}
