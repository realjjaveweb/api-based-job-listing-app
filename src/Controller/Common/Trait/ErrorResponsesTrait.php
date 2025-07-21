<?php

declare(strict_types=1);

namespace App\Controller\Common\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Dto\Controller\ErrorResponseDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

trait ErrorResponsesTrait
{
    private readonly LoggerInterface $logger;
    private readonly SerializerInterface $serializer;

    private function handleNotFound(string $message): JsonResponse
    {
        return new JsonResponse(
            data: $this->serializer->serialize(
                new ErrorResponseDto(success: false, error: $message),
                'json'
            ),
            status: Response::HTTP_NOT_FOUND,
            headers: [],
            json: true
        );
    }

    /** @param array<string, mixed> $context */
    private function handleKnownException(\Exception $error, string $message, array $context = []): JsonResponse
    {
        $this->logger->error($message, [
            'error' => $error,
            'error_message' => $error->getMessage(),
            ...$context,
        ]);
        $errorDto = new ErrorResponseDto(
            success: false,
            error: $error->getMessage(),
            message: $message,
        );
        $json = $this->serializer->serialize($errorDto, 'json');
        $customCode = $error->getCode();
        $customCode = (\is_int($customCode) && ($customCode >= 300) && ($customCode < 500)) ? $customCode : null;
        return new JsonResponse(
            data: $json,
            status: $customCode ?? Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: [],
            json: true,
        );
    }

    private function handleUnexpectedError(\Throwable $error): JsonResponse
    {
        // this seems like duplicating the message, but can be useful for filtering the logs later
        $this->logger->error($error->getMessage(), ['error' => $error, 'error_message' => $error->getMessage()]);
        return new JsonResponse(
            data: $this->serializer->serialize(
                new ErrorResponseDto(
                    success: false,
                    error: 'Internal server error',
                    message: $error->getMessage()
                ),
                'json'
            ),
            status: Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: [],
            json: true
        );
    }
}
