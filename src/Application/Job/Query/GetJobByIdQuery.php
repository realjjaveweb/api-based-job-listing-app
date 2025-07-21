<?php

declare(strict_types=1);

namespace App\Application\Job\Query;

use App\Domain\Job\ValueObject\JobId;

final class GetJobByIdQuery
{
    public function __construct(
        private readonly JobId $jobId
    ) {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }
}
