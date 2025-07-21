<?php

declare(strict_types=1);

namespace App\Application\Job\Repository;

use App\Domain\Job\JobApplication;

interface JobApplicationRepositoryInterface
{
    public function save(JobApplication $application): void;
}
