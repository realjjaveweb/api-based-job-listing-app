<?php

declare(strict_types=1);

namespace App\Infrastructure\Recruitis;

use App\Application\Job\Repository\JobRepositoryInterface;
use App\Domain\Job\Job;
use App\Dto\Controller\JobListWithMetaDto;

final class RecruitisJobRepository implements JobRepositoryInterface
{
    public function __construct(
        private readonly RecruitisApiClient $apiClient
    ) {
    }

    /**
     */
    public function getJobs(int $page = 1, int $limit = 10): JobListWithMetaDto
    {
        return $this->apiClient->getJobs($page, $limit);
    }

    public function getJobById(string $id): ?Job
    {
        return $this->apiClient->getJobById($id);
    }
}
