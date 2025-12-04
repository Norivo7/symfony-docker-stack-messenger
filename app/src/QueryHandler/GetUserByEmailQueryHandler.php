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

    public function __invoke(GetUserByEmailQuery $query): array
    {
        $user = $this->userRepository->findByEmail($query->email);

        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
