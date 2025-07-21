<?php

declare(strict_types=1);

namespace App\Domain\Job;

use App\Domain\Job\ValueObject\JobId;
use App\Domain\Job\ValueObject\JobTitle;
use App\Domain\Job\ValueObject\Salary;
use App\Domain\Job\ValueObject\JobDescription;

class Job
{
    public function __construct(
        private readonly JobId $id,
        private readonly JobTitle $title,
        private readonly ?Salary $salary,
        private readonly JobDescription $description,
        private readonly \DateTimeImmutable $createdAt,
        private readonly \DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): JobId
    {
        return $this->id;
    }

    public function getTitle(): JobTitle
    {
        return $this->title;
    }

    public function getSalary(): ?Salary
    {
        return $this->salary;
    }

    public function getDescription(): JobDescription
    {
        return $this->description;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
