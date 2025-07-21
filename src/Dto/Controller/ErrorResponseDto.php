<?php

declare(strict_types=1);

namespace App\Dto\Controller;

readonly class ErrorResponseDto
{
    public function __construct(
        public bool $success,
        public string $error,
        public ?string $message = null
    ) {
    }
}
