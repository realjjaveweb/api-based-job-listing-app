<?php

declare(strict_types=1);

namespace App\Application\Job\Service;

use App\Application\Job\Repository\JobApplicationRepositoryInterface;
use App\Domain\Job\ValueObject\JobId;
use App\Dto\Controller\JobApplicationRequestDto;

final class JobApplicationService
{
    public function __construct(
        private readonly JobApplicationRepositoryInterface $repository,
        private readonly JobApplicationMapper $mapper
    ) {
    }

    public function submitApplication(JobId $jobId, JobApplicationRequestDto $dto): void
    {
        $application = $this->mapper->mapJobApplicationRequestDtoToDomainJobApplication($jobId, $dto);
        $this->repository->save($application);
    }
}
