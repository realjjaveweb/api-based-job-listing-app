<?php

declare(strict_types=1);

namespace App\Dto\Controller;

use App\Domain\Job\Job;

readonly class JobListWithMetaDto
{
    /**
     * @param list<Job> $jobs
     */
    public function __construct(
        public array $jobs,
        public JobListMetaDto $meta
    ) {
    }
}
