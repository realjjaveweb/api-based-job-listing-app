<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function get(
        string $url,
        array $queryParams = [],
        array $headers = []
    ): array {
        try {
            $response = $this->client->get($url, [
                'query' => $queryParams,
                'headers' => $headers,
            ]);

            $content = $response->getBody()->getContents();
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $data ?? [];
        } catch (GuzzleException $e) {
            $this->logger->error('HTTP GET request failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function post(
        string $url,
        array $data = [],
        array $headers = []
    ): array {
        try {
            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => $headers,
            ]);

            $content = $response->getBody()->getContents();
            $responseData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $responseData ?? [];
        } catch (GuzzleException $e) {
            $this->logger->error('HTTP POST request failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            if ($e instanceof ClientException) {
                throw match ($e->getCode()) {
                    400 => new BadRequestHttpException(code: 400, previous: $e),
                    404 => new NotFoundHttpException(code: 404, previous: $e),
                    default => new HttpException($e->getCode(), 'HTTP request failed: ' . $e->getMessage(), previous:$e),
                };
            }

            throw new \RuntimeException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
