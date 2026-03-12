<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class JsonPlaceholderUserClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function fetchUsers(): array
    {
        $response = $this->httpClient->request(
            'GET',
            'https://jsonplaceholder.typicode.com/users'
        );

        return $response->toArray();
    }
}
