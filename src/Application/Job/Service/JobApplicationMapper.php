<?php

declare(strict_types=1);

namespace App\Application\Job\Service;

use App\Domain\Job\JobApplication;
use App\Domain\Job\ValueObject\JobId;
use App\Dto\Controller\JobApplicationRequestDto;

final class JobApplicationMapper
{
    public function mapJobApplicationRequestDtoToDomainJobApplication(JobId $jobId, JobApplicationRequestDto $dto): JobApplication
    {
        return new JobApplication(
            $jobId,
            $dto->firstName ?? '',
            $dto->lastName ?? '',
            $dto->email ?? '',
            $dto->phone ?? '',
            $dto->coverLetter ?? '',
            $dto->resumeUrl
        );
    }
}
