<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\models;

use Sanweb\Taskforce\enum\TaskStatus;

use InvalidArgumentException;

final readonly class Task
{
    public function __construct(
        private TaskStatus $status,
        private int $customerId,
        private ?int $executorId = null
    ) {
        if ($customerId <= 0) {
            throw new InvalidArgumentException(sprintf(
                'Customer ID must be positive; %d given',
                $customerId,
            ));
        }

        if ($executorId !== null && $executorId <= 0) {
            throw new InvalidArgumentException(sprintf(
                'Executor ID must be positive; %d given',
                $executorId,
            ));
        }
    }

    public function withStatus(TaskStatus $status): self
    {
        return new self(
            status: $status,
            customerId: $this->customerId,
            executorId: $this->executorId,
        );
    }

    public function withExecutor(int $executorId): self
    {
        return new self(
            status: $this->status,
            customerId: $this->customerId,
            executorId: $executorId,
        );
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getExecutorId(): ?int
    {
        return $this->executorId;
    }
}
