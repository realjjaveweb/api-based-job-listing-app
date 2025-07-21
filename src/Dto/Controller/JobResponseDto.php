<?php

declare(strict_types=1);

namespace App\Dto\Controller;

readonly class JobResponseDto
{
    public function __construct(
        public bool $success,
        public ?JobDto $data = null
    ) {
    }
}
