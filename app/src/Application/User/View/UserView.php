<?php
declare(strict_types=1);

namespace App\Application\User\View;

final readonly class UserView
{
    public function __construct(
        public int    $id,
        public string $email,
    ) {}

}
