<?php

declare(strict_types=1);

namespace App\User\Application\QueryHandler;

use App\User\Application\Query\GetUserByIdQuery;
use App\User\Domain\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUserByIdQueryHandler
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(GetUserByIdQuery $query): array
    {
        $user = $this->userRepository->findById($query->id);

        if (!$user) {
            throw new \RuntimeException("User with ID $query->id not found.");
        }

        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
