<?php
declare(strict_types=1);

namespace App\QueryHandler;

use App\Domain\User\UserRepositoryInterface;
use App\Query\GetUserByIdQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUserByIdQueryHandler
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function __invoke(GetUserByIdQuery $query): array
    {
        $user = $this->userRepository->findById($query->id);
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
