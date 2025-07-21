<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Job;

use App\Domain\Job\Job;
use App\Domain\Job\ValueObject\JobId;
use App\Domain\Job\ValueObject\JobTitle;
use App\Domain\Job\ValueObject\Salary;
use App\Domain\Job\ValueObject\JobDescription;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    public function testJobCreationWithAllFields(): void
    {
        $jobId = new JobId('123');
        $title = new JobTitle('Software Engineer');
        $salary = new Salary('50000 CZK');
        $description = new JobDescription('We are looking for a talented software engineer...');
        $createdAt = new \DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new \DateTimeImmutable('2024-01-01 10:00:00');

        $job = new Job($jobId, $title, $salary, $description, $createdAt, $updatedAt);

        $this->assertEquals($jobId, $job->getId());
        $this->assertEquals($title, $job->getTitle());
        $this->assertEquals($salary, $job->getSalary());
        $this->assertEquals($description, $job->getDescription());
        $this->assertEquals($createdAt, $job->getCreatedAt());
        $this->assertEquals($updatedAt, $job->getUpdatedAt());
    }

    public function testJobCreationWithoutSalary(): void
    {
        $jobId = new JobId('123');
        $title = new JobTitle('Software Engineer');
        $description = new JobDescription('We are looking for a talented software engineer...');
        $createdAt = new \DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new \DateTimeImmutable('2024-01-01 10:00:00');

        $job = new Job($jobId, $title, null, $description, $createdAt, $updatedAt);

        $this->assertNull($job->getSalary());
    }

    public function testJobCreationWithAllRequiredFields(): void
    {
        $jobId = new JobId('123');
        $title = new JobTitle('Software Engineer');
        $salary = new Salary('50000 CZK');
        $description = new JobDescription('We are looking for a talented software engineer...');
        $createdAt = new \DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new \DateTimeImmutable('2024-01-01 10:00:00');

        $job = new Job($jobId, $title, $salary, $description, $createdAt, $updatedAt);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($jobId, $job->getId());
        $this->assertEquals($title, $job->getTitle());
        $this->assertEquals($salary, $job->getSalary());
        $this->assertEquals($description, $job->getDescription());
        $this->assertEquals($createdAt, $job->getCreatedAt());
        $this->assertEquals($updatedAt, $job->getUpdatedAt());
    }
} 