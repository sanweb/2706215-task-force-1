<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\models;

use InvalidArgumentException;

final readonly class User
{
    public function __construct(
        private int $id,
        private bool $isExecutor,
    )
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(sprintf(
                'User ID must be positive; %d given',
                $id,
            ));
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIsExecutor(): bool
    {
        return $this->isExecutor;
    }
}
