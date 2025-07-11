<?php
declare(strict_types=1);

namespace App\Query;

final readonly class GetUserByIdQuery
{
    public function __construct(public int $userId) {}
}
