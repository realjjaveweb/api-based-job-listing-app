<?php

declare(strict_types=1);

namespace App\Infrastructure\Recruitis;

use App\Application\Job\Repository\JobApplicationRepositoryInterface;
use App\Domain\Job\JobApplication;
use App\Dto\Application\JobApplicationDto;
use Psr\Log\LoggerInterface;

final class RecruitisJobApplicationRepository implements JobApplicationRepositoryInterface
{
    public function __construct(
        private readonly RecruitisApiClient $apiClient,
        private readonly LoggerInterface $logger
    ) {
    }

    public function save(JobApplication $application): void
    {
        try {
            $applicationDto = new JobApplicationDto(
                jobId: $application->getJobId()->getValue(),
                firstName: $application->getFirstName(),
                lastName: $application->getLastName(),
                email: $application->getEmail(),
                phone: $application->getPhone(),
                coverLetter: $application->getCoverLetter(),
                resumeUrl: $application->getResumeUrl(),
                submittedAt: $application->getSubmittedAt()->format('Y-m-d H:i:s')
            );

            $this->apiClient->submitApplication($applicationDto);
            $this->logger->info('Job application saved successfully', [
                'job_id' => $applicationDto->jobId
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to save job application', [
                'job_id' => $application->getJobId()->getValue(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
