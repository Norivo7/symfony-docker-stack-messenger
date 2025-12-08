<?php

declare(strict_types=1);

namespace App\User\Domain\Contracts;

use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;
}
