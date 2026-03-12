<?php

declare(strict_types=1);

namespace App\User\Infrastructure\InMemory\Repository;

use App\User\Domain\Contracts\UserRepositoryInterface;
use App\User\Domain\Entity\User;

final class InMemoryUserRepository implements UserRepositoryInterface
{
    /** @var User[] */
    private array $users;

    public function __construct()
    {
        $this->users = [
            new User(1, 'john@doe.com', 'John Doe'),
            new User(2, 'jane@doe.com', 'Jane Doe'),
        ];
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }

        return null;
    }

    public function findById(int $id): ?User
    {
        foreach ($this->users as $user) {
            if ($user->id === $id) {
                return $user;
            }
        }

        return null;
    }

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
