<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Job\Service;

use App\Application\Job\Service\JobDtoMapper;
use App\Domain\Job\Job;
use App\Domain\Job\ValueObject\JobId;
use App\Domain\Job\ValueObject\JobTitle;
use App\Domain\Job\ValueObject\Salary;
use App\Domain\Job\ValueObject\JobDescription;
use App\Dto\Controller\JobDto;
use PHPUnit\Framework\TestCase;

class JobDtoMapperTest extends TestCase
{
    private JobDtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new JobDtoMapper();
    }

    public function testMapToDto(): void
    {
        $jobId = new JobId('123');
        $title = new JobTitle('Software Engineer');
        $salary = new Salary('50000 CZK');
        $description = new JobDescription('We are looking for a talented software engineer...');
        $createdAt = new \DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new \DateTimeImmutable('2024-01-01 10:00:00');

        $job = new Job($jobId, $title, $salary, $description, $createdAt, $updatedAt);

        $dto = $this->mapper->mapDomainJobToApiJobDto($job);

        $this->assertInstanceOf(JobDto::class, $dto);
        $this->assertEquals('123', $dto->id);
        $this->assertEquals('Software Engineer', $dto->title);
        $this->assertEquals('50000 CZK', $dto->salary);
        $this->assertEquals('We are looking for a talented software engineer...', $dto->description);
        $this->assertEquals('2024-01-01 10:00:00', $dto->createdAt);
        $this->assertEquals('2024-01-01 10:00:00', $dto->updatedAt);
    }

    public function testMapToDtoWithoutSalary(): void
    {
        $jobId = new JobId('123');
        $title = new JobTitle('Software Engineer');
        $description = new JobDescription('We are looking for a talented software engineer...');
        $createdAt = new \DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new \DateTimeImmutable('2024-01-01 10:00:00');

        $job = new Job($jobId, $title, null, $description, $createdAt, $updatedAt);

        $dto = $this->mapper->mapDomainJobToApiJobDto($job);

        $this->assertInstanceOf(JobDto::class, $dto);
        $this->assertNull($dto->salary);
    }

    public function testMapToDtoArray(): void
    {
        $job1 = new Job(
            new JobId('1'),
            new JobTitle('Job 1'),
            new Salary('1000'),
            new JobDescription('Description 1'),
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-01-01')
        );

        $job2 = new Job(
            new JobId('2'),
            new JobTitle('Job 2'),
            null,
            new JobDescription('Description 2'),
            new \DateTimeImmutable('2024-01-02'),
            new \DateTimeImmutable('2024-01-02')
        );

        $jobs = [$job1, $job2];
        $dtos = $this->mapper->mapDomainJobListToApiJobDtoList($jobs);

        $this->assertCount(2, $dtos);
        $this->assertInstanceOf(JobDto::class, $dtos[0]);
        $this->assertInstanceOf(JobDto::class, $dtos[1]);
        $this->assertEquals('1', $dtos[0]->id);
        $this->assertEquals('2', $dtos[1]->id);
    }
} 