<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\models;

use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\UserRole;

use RuntimeException;
use InvalidArgumentException;

final class Task
{
    private TaskStatus $status;
    private int $customerId;
    private ?int $executorId;

    public function __construct(
        TaskStatus $status,
        int $customerId,
        ?int $executorId = null
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

        $this->status = $status;
        $this->customerId = $customerId;
        $this->executorId = $executorId;
    }

    public function getActionNextStatus(TaskAction $action): TaskStatus
    {
        return match ($action) {
            TaskAction::Create => TaskStatus::New,
            TaskAction::Cancel => TaskStatus::Canceled,
            TaskAction::Assign => TaskStatus::InProgress,
            TaskAction::Complete => TaskStatus::Completed,
            TaskAction::Refuse => TaskStatus::Failed,

            // Actions that do not change the status
            TaskAction::Bid => $this->status,
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
                TaskAction::Create,
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
            throw new RuntimeException(
                "User with role {$userRole->value} cannot perform action {$action->value}"
            );
        }

        if (!in_array($action, $this->getAvailableActions(), true)) {
            throw new RuntimeException(
                "Action {$action->value} is unavailable for status {$this->status->value}."
            );
        }

        $this->status = $this->getActionNextStatus($action);

        return $this->status;
    }
}
