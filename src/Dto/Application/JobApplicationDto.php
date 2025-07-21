<?php

declare(strict_types=1);

namespace App\Dto\Application;

readonly class JobApplicationDto
{
    public function __construct(
        public string $jobId,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $phone,
        public string $coverLetter,
        public ?string $resumeUrl,
        public string $submittedAt
    ) {
    }
}
