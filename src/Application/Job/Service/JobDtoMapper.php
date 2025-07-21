<?php

declare(strict_types=1);

namespace App\Application\Job\Service;

use App\Domain\Job\Job;
use App\Dto\Controller\JobDto;

class JobDtoMapper
{
    public function mapDomainJobToApiJobDto(Job $job): JobDto
    {
        return new JobDto(
            id: $job->getId()->getValue(),
            title: $job->getTitle()->getValue(),
            salary: $job->getSalary()?->getValue(),
            description: $job->getDescription()->getValue(),
            createdAt: $job->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $job->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }

    /**
     * @param list<Job> $jobs
     * @return list<JobDto>
     */
    public function mapDomainJobListToApiJobDtoList(array $jobs): array
    {
        return array_map(fn (Job $job) => $this->mapDomainJobToApiJobDto($job), $jobs);
    }
}
