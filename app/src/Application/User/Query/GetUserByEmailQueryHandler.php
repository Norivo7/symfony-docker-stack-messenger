<?php
declare(strict_types=1);

namespace App\Application\User\Query;

use App\Application\User\View\UserView;
use App\Domain\User\UserRepositoryInterface;
use App\Query\GetUserByEmailQuery;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final readonly class GetUserByEmailQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}
    public function __invoke(GetUserByEmailQuery $query): UserView
    {
        $user = $this->repository->findByEmail($query->email);

        if (!$user) {
            throw new UserNotFoundException('User ' . $query->email . ' not found.');
        }

        return new UserView($user->id, $user->email);
    }
}
