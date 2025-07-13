<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

}
