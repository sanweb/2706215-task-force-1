<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;

/**
 * Cancels the task.
 */
final class CancelTaskAction extends BaseTaskAction
{
    #[Override]
    public function getName(): string
    {
        return TaskAction::Cancel->value;
    }

    #[Override]
    public function getLabel(): string
    {
        return TaskAction::Cancel->label();
    }

    #[Override]
    public function isAllowed(int $customerId, ?int $executorId, int $userId): bool
    {
        return $customerId === $userId && $executorId === null;
    }
}
