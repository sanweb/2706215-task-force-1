<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

/**
 * Refuses the task.
 */
final class RefuseTaskAction extends BaseTaskAction
{
    #[Override]
    public function getAction(): TaskAction
    {
        return TaskAction::Refuse;
    }

    #[Override]
    public function getNextStatus(): ?TaskStatus
    {
        return TaskStatus::Failed;
    }

    #[Override]
    public function isAllowed(
        Task $task,
        User $user,
    ): bool {
        return $task->getExecutorId() === $user->getId();
            // $task->getExecutorId() !== null
            // && $task->getExecutorId() === $user->getId();
    }
}
