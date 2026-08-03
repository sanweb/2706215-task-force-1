<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use InvalidArgumentException;
use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\exception\TaskActionException;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

/**
 * Assigns an executor to the task.
 */
final class AssignTaskAction extends BaseTaskAction
{
    #[Override]
    public function getAction(): TaskAction
    {
        return TaskAction::Assign;
    }

    #[Override]
    public function getNextStatus(): ?TaskStatus
    {
        return TaskStatus::InProgress;
    }

    #[Override]
    public function isAllowed(
        Task $task,
        User $user,
    ): bool {
        return $task->getCustomerId() === $user->getId();
            // && $task->getExecutorId() === null;
    }

    #[Override]
    public function execute(
        Task $task,
        User $user,
        array $parameters = []
    ): Task {
        $executorId = filter_var(
            $parameters['executor_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]],
        );

        if ($executorId === false) {
            throw new InvalidArgumentException(
                'Executor ID must be a positive integer.',
            );
        }

        if ($executorId === $task->getCustomerId()) {
            throw new TaskActionException(
                'Customer cannot be assigned as executor.',
            );
        }

        return $task->withExecutor($executorId);
    }
}
