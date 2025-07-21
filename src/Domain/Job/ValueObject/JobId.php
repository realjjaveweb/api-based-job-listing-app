<?php

declare(strict_types=1);

namespace App\Domain\Job\ValueObject;

final class JobId
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new \InvalidArgumentException('Job ID cannot be empty');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(JobId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
