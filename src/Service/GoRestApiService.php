<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoRestApiService
{
    private $httpClient;
    private $apiToken;
    private $apiUrl;

    public function __construct(HttpClientInterface $httpClient, string $apiToken = null)
    {
        $this->httpClient = $httpClient;
        $this->apiToken = $apiToken ?? $_ENV['GOREST_API_TOKEN'];
        $this->apiUrl = 'https://gorest.co.in/public/v2';
    }

    public function getUsers(int $page = 1, string $search = ''): array
    {
        $query = ['page' => $page];

        if (!empty($search)) {
            $query['name'] = $search;
        }

        $response = $this->httpClient->request('GET', "{$this->apiUrl}/users", [
            'query' => $query
        ]);

        return $response->toArray();
    }

    public function getUser(int $id): ?array
    {
        try {
            $response = $this->httpClient->request('GET', "{$this->apiUrl}/users/{$id}");
            return $response->toArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPosts(int $userId = null, int $page = 1): array
    {
        $query = ['page' => $page];

        if ($userId) {
            $query['user_id'] = $userId;
        }

        $response = $this->httpClient->request('GET', "{$this->apiUrl}/posts", [
            'query' => $query
        ]);

        return $response->toArray();
    }

    public function getPost(int $id): ?array
    {
        try {
            $response = $this->httpClient->request('GET', "{$this->apiUrl}/posts/{$id}");
            return $response->toArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updatePost(int $id, array $data): array
    {
        try {
            $response = $this->httpClient->request('PUT', "{$this->apiUrl}/posts/{$id}", [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiToken}",
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
