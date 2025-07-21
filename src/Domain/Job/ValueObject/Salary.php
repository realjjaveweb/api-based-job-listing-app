<?php

declare(strict_types=1);

namespace App\Domain\Job\ValueObject;

final class Salary
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Salary cannot be empty');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
