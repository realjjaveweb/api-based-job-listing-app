<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

interface HttpClientInterface
{
    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function get(
        string $url,
        array $queryParams = [],
        array $headers = []
    ): array;

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function post(
        string $url,
        array $data = [],
        array $headers = []
    ): array;
}
