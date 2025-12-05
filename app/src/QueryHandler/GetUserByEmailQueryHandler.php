<?php

declare(strict_types=1);

namespace App\QueryHandler;

use App\Domain\User\UserRepositoryInterface;
use App\Query\GetUserByEmailQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUserByEmailQueryHandler
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(GetUserByEmailQuery $query): array
    {
        $user = $this->userRepository->findByEmail($query->email);

        if (null === $user) {
            throw new \RuntimeException("User with email {$query->email} not found.");
        }

        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
