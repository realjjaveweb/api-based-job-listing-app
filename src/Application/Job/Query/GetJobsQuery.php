<?php

declare(strict_types=1);

namespace App\Application\Job\Query;

final class GetJobsQuery
{
    public function __construct(
        private readonly int $page = 1,
        private readonly int $limit = 10
    ) {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page must be greater than 0');
        }
        if ($limit < 1 || $limit > 100) {
            throw new \InvalidArgumentException('Limit must be between 1 and 100');
        }
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
