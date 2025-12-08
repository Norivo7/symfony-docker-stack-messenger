<?php

declare(strict_types=1);

namespace App\User\Domain;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;
}
