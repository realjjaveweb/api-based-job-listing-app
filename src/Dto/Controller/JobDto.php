<?php

declare(strict_types=1);

namespace App\Dto\Controller;

readonly class JobDto
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $salary,
        public string $description,
        public string $createdAt,
        public string $updatedAt
    ) {
    }
}
