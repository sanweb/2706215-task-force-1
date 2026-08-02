<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

/**
 * Makes a bid on the task.
 */
final class BidTaskAction extends BaseTaskAction
{
    #[Override]
    public function getAction(): TaskAction
    {
        return TaskAction::Bid;
    }

    #[Override]
    public function getNextStatus(): ?TaskStatus
    {
        return null;
    }

    #[Override]
    public function isAllowed(
        Task $task,
        User $user,
    ): bool {
        return $task->getCustomerId() !== $user->getId()
            && $user->getIsExecutor(); // can bid
            // && $task->getExecutorId() === null;
    }
}
