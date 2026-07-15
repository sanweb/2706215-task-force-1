<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\models;

use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\UserRole;

use InvalidArgumentException;
use Sanweb\Taskforce\components\TaskStatus\TaskStatusFactory;
use Sanweb\Taskforce\exception\TaskActionException;
use Sanweb\Taskforce\interface\TaskStatusInterface;

final class Task
{
    // ?
    private TaskStatus $status;
    private TaskStatusInterface $state;

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

    private function getNextStatusByAction(TaskAction $action): TaskStatus
    {
        return match ($action) {
            TaskAction::Cancel => $this->state->cancel(),
            TaskAction::Assign => $this->state->assign(),
            TaskAction::Complete => $this->state->complete(),
            TaskAction::Refuse => $this->state->refuse(),

            // ?
            // Actions that do not change the status
            TaskAction::Bid => $this->state->bid(),

            // ?
            // Actions that cannot be applied to the task
            TaskAction::Create => throw new TaskActionException(
                'The create action cannot be applied to an existing task.'
            ),
        };
    }

    public function getAvailableActions(): array
    {
        return $this->state->getAvailableActions();
    }

    // Move to auth service?
    public function getAllowedActions(UserRole $userRole): array
    {
        return match ($userRole) {
            UserRole::Customer => [
                //TaskAction::Create,
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

        // ?
        if (!in_array($action, $this->getAvailableActions(), true)) {
            throw new TaskActionException(
                "Action {$action->value} is unavailable for status {$this->status->value}."
            );
        }

        $nextStatus = $this->getNextStatusByAction($action);

        if ($nextStatus !== $this->getStatus()) {
            $this->setStatus($nextStatus);
        }

        return $this->getStatus();
    }

    // transitionTo
    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
        $this->state = TaskStatusFactory::create($status, $this);
    }

    // ?
    public function getStatus(): TaskStatus
    {
        return $this->status;
        //return $this->state->getStatus();
    }
}
