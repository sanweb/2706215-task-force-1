<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\models;

use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\UserRole;

use InvalidArgumentException;
use Sanweb\Taskforce\exception\TaskActionException;

final class Task
{
    private TaskStatus $status;

    public function __construct(
        TaskStatus $status,
        private int $customerId,
        private ?int $executorId = null
    ) {
        // Validate $customerId and $executorId
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

        // Set state
        $this->setStatus($status);
    }

    public function getActionNextStatus(TaskAction $action): TaskStatus
    {
        return match ($action) {
            TaskAction::Create => TaskStatus::New,
            TaskAction::Cancel => TaskStatus::Canceled,
            TaskAction::Assign => TaskStatus::InProgress,
            TaskAction::Complete => TaskStatus::Completed,
            TaskAction::Refuse => TaskStatus::Failed,

            // ?
            // Actions that do not change the status
            TaskAction::Bid => $this->status,

            // ?
            // Actions that cannot be applied to the task
            /*
            TaskAction::Create => throw new TaskActionException(
                'The create action cannot be applied to an existing task.'
            ),
            */
        };
    }

    public function getAvailableActions(): array
    {
        return match ($this->status) {
            TaskStatus::New => [
                TaskAction::Cancel,
                TaskAction::Bid,
                TaskAction::Assign,
            ],
            TaskStatus::InProgress => [
                TaskAction::Complete,
                TaskAction::Refuse,
            ],
            TaskStatus::Canceled,
            TaskStatus::Completed,
            TaskStatus::Failed => [],
        };
    }

    public function getAllowedActions(UserRole $userRole): array
    {
        return match ($userRole) {
            UserRole::Customer => [
                TaskAction::Create, // ?
                TaskAction::Cancel,
                TaskAction::Assign,
                TaskAction::Complete,
            ],
            UserRole::Executor => [
                TaskAction::Bid,
                TaskAction::Refuse,
            ],
        };
    }

    public function act(TaskAction $action, UserRole $userRole): TaskStatus
    {
        if (!in_array($action, $this->getAllowedActions($userRole), true)) {
            throw new TaskActionException(
                "User with role {$userRole->value} cannot perform action {$action->value}"
            );
        }

        if (!in_array($action, $this->getAvailableActions(), true)) {
            throw new TaskActionException(
                "Action {$action->value} is unavailable for status {$this->status->value}."
            );
        }

        $this->setStatus($this->getActionNextStatus($action));

        return $this->status;
    }

    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getStatusName(): string
    {
        return $this->status->name;
    }

    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function getStatusLabel(): string
    {
        return $this->status->label();
    }
}
