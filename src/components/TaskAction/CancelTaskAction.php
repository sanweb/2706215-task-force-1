<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

/**
 * Cancels the task.
 */
final class CancelTaskAction extends BaseTaskAction
{
    #[Override]
    public function getAction(): TaskAction
    {
        return TaskAction::Cancel;
    }

    #[Override]
    public function getNextStatus(): ?TaskStatus
    {
        return TaskStatus::Canceled;
    }

    #[Override]
    public function isAllowed(
        Task $task,
        User $user,
    ): bool {
        return $task->getCustomerId() === $user->getId();
            // && $task->getExecutorId() === null;
    }
}
