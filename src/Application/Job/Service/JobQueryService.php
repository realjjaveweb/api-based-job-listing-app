<?php

declare(strict_types=1);

namespace App\Application\Job\Service;

use App\Application\Job\Query\GetJobsQuery;
use App\Application\Job\Query\GetJobByIdQuery;
use App\Application\Job\Repository\JobRepositoryInterface;
use App\Domain\Job\Job;
use App\Dto\Controller\JobListWithMetaDto;

final class JobQueryService
{
    public function __construct(
        private readonly JobRepositoryInterface $jobRepository
    ) {
    }

    /**
     */
    public function getJobs(GetJobsQuery $query): JobListWithMetaDto
    {
        return $this->jobRepository->getJobs($query->getPage(), $query->getLimit());
    }

    public function getJobById(GetJobByIdQuery $query): ?Job
    {
        return $this->jobRepository->getJobById($query->getJobId()->getValue());
    }
}
