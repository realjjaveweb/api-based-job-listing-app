<?php

declare(strict_types=1);

namespace App\Dto\Controller;

readonly class JobApplicationRequestDto
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $coverLetter = null,
        public ?string $resumeUrl = null
    ) {
    }
}
