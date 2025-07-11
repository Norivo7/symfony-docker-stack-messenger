<?php
declare(strict_types=1);

namespace App\QueryHandler;

use App\Query\GetUserByIdQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUserByIdQueryHandler
{
    public function __invoke(GetUserByIdQuery $query): array
    {
        return ['id' => $query->userId, 'name' => 'Kamil'];
    }
}
