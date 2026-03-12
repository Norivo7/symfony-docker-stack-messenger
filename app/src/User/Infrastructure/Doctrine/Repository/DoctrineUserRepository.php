<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine\Repository;

use App\User\Domain\Contracts\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Doctrine\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(User $user): void
    {
        $entity = $this->entityManager->find(UserEntity::class, $user->id);

        if (!$entity instanceof UserEntity) {
            $entity = new UserEntity(
                id: $user->id,
                email: $user->email,
                name: $user->name,
            );

            $this->entityManager->persist($entity);

            return;
        }

        $entity->setEmail($user->email);
        $entity->setName($user->name);
    }

    public function findById(int $id): ?User
    {
        $entity = $this->entityManager->find(UserEntity::class, $id);

        if (!$entity instanceof UserEntity) {
            return null;
        }

        return new User(
            id: $entity->getId(),
            email: $entity->getEmail(),
            name: $entity->getName(),
        );
    }

    public function findByEmail(string $email): ?User
    {
        $entity = $this->entityManager
            ->getRepository(UserEntity::class)
            ->findOneBy(['email' => $email]);

        if (!$entity instanceof UserEntity) {
            return null;
        }

        return new User(
            id: $entity->getId(),
            email: $entity->getEmail(),
            name: $entity->getName(),
        );
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
