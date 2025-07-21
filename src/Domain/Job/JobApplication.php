<?php

declare(strict_types=1);

namespace App\Domain\Job;

use App\Domain\Job\ValueObject\JobId;

final class JobApplication
{
    private readonly string $firstName;
    private readonly string $lastName;
    private readonly string $email;
    private readonly string $phone;
    private readonly string $coverLetter;
    private readonly ?string $resumeUrl;

    public function __construct(
        private readonly JobId $jobId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $coverLetter,
        ?string $resumeUrl,
        private readonly \DateTimeImmutable $submittedAt = new \DateTimeImmutable()
    ) {
        $this->firstName = \trim($firstName);
        $this->lastName = \trim($lastName);
        $this->validateName();

        $this->email = \trim($email);
        $this->validateEmail();

        $this->phone = \trim($phone);
        $this->validatePhone();

        $this->coverLetter = \trim($coverLetter);
        $this->validateCoverLetter();

        $resumeUrl = ($resumeUrl === null) ? null : \trim($resumeUrl);
        $this->resumeUrl = ($resumeUrl === '') ? null : $resumeUrl;
    }

    private function validateName(): void
    {
        if (($this->firstName === '') && ($this->lastName === '')) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
    }

    private function validateEmail(): void
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    private function validatePhone(): void
    {
        if ($this->phone === '') {
            throw new \InvalidArgumentException('Phone number cannot be empty');
        }
    }

    private function validateCoverLetter(): void
    {
        if ($this->coverLetter === '') {
            throw new \InvalidArgumentException('Cover letter cannot be empty');
        }
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCoverLetter(): string
    {
        return $this->coverLetter;
    }

    public function getResumeUrl(): ?string
    {
        return $this->resumeUrl;
    }

    public function getSubmittedAt(): \DateTimeImmutable
    {
        return $this->submittedAt;
    }
}
