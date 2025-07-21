<?php

declare(strict_types=1);

namespace App\Application\Job\Repository;

use App\Domain\Job\Job;
use App\Dto\Controller\JobListWithMetaDto;

interface JobRepositoryInterface
{
    /**
     */
    public function getJobs(int $page = 1, int $limit = 10): JobListWithMetaDto;

    public function getJobById(string $id): ?Job;
}
