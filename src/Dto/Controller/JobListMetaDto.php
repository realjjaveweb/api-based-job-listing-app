<?php

declare(strict_types=1);

namespace App\Dto\Controller;

readonly class JobListMetaDto
{
    public function __construct(
        public int $page,
        public int $limit,
        public int $total
    ) {
    }
}
