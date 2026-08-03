<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

/**
 * Completes the task.
 */
final class CompleteTaskAction extends BaseTaskAction
{
    #[Override]
    public function getAction(): TaskAction
    {
        return TaskAction::Complete;
    }

    #[Override]
    public function getNextStatus(): ?TaskStatus
    {
        return TaskStatus::Completed;
    }

    #[Override]
    public function isAllowed(
        Task $task,
        User $user,
    ): bool {
        return $task->getCustomerId() === $user->getId();
            // && $task->getExecutorId() !== null;
    }
}
