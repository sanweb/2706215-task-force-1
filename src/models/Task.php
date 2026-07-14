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

    public function __construct(TaskStatus $status, int $customerId, ?int $executorId = null)
    {
        if ($customerId > 0) {
            $this->status = $status;
            $this->customerId = $customerId;
            $this->executorId = $executorId > 0 ? $executorId : null;
        } else {
            throw new InvalidArgumentException(sprintf(
                'Invalid task data (status: %s, customerId: %d, executorId: %s)',
                $status->value,
                $customerId,
                $executorId === null ? 'null' : (string) $executorId
            ));
        }
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
        $availableActions = [];

        if ($this->status === TaskStatus::New) {
            $availableActions = [
                TaskAction::Cancel,
                TaskAction::Bid,
                TaskAction::Assign,
            ];
        } elseif ($this->status === TaskStatus::InProgress) {
            $availableActions = [
                TaskAction::Complete,
                TaskAction::Refuse,
            ];
        }

        return $availableActions;
    }

    public function getAllowedActions(UserRole $userRole): array
    {
        $allowedActions = [];

        if ($userRole === UserRole::Customer) {
            $allowedActions = [
                TaskAction::Create,
                TaskAction::Cancel,
                TaskAction::Assign,
                TaskAction::Complete,
            ];
        } elseif ($userRole === UserRole::Executor) {
            $allowedActions = [
                TaskAction::Bid,
                TaskAction::Refuse,
            ];
        }

        return $allowedActions;
    }

    public function act(TaskAction $action, UserRole $userRole): TaskStatus
    {
        if (
            !in_array($action, $this->getAvailableActions(), true)
            || !in_array($action, $this->getAllowedActions($userRole), true)
        ) {
            throw new RuntimeException("Cannot perform {$action->value} by {$userRole->value} on status {$this->status->value}");
        }

        $this->status = self::getActionNextStatus($action) ?: $this->status;

        return $this->status;
    }
}
