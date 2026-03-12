<?php

declare(strict_types=1);

namespace App\User\Application\Messenger\CommandHandler;

use App\User\Application\Messenger\Command\ImportUsersCommand;
use App\User\Domain\Contracts\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Integration\JsonPlaceholderUserClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ImportUsersHandler
{
    public function __construct(
        private JsonPlaceholderUserClient $jsonPlaceholderUserClient,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(ImportUsersCommand $command): void
    {
        $users = $this->jsonPlaceholderUserClient->fetchUsers();

        foreach ($users as $userData) {
            $id = $userData['id'] ?? null;
            $email = $userData['email'] ?? null;
            $name = $userData['name'] ?? null;

            if (!is_int($id) || !is_string($email) || !is_string($name)) {
                continue;
            }

            $user = new User(
                id: $id,
                email: $email,
                name: $name,
            );

            $this->userRepository->save($user);
        }

        $this->userRepository->flush();
    }
}
