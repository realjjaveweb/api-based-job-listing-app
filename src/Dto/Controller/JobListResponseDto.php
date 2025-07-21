<?php

declare(strict_types=1);

namespace App\Dto\Controller;

readonly class JobListResponseDto
{
    public function __construct(
        public bool $success,
        /** @var JobDto[] */
        public array $data,
        public JobListMetaDto $pagination
    ) {
    }
}
