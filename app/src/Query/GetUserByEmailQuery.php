<?php
declare(strict_types=1);

namespace App\Query;

final readonly class GetUserByEmailQuery
{
    public function __construct(
        public string $email
    ) {}
}
