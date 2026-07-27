<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\enum\TaskAction;

/**
 * Completes the task.
 */
final class CompleteTaskAction extends BaseTaskAction
{
    #[\Override]
    public function getName(): string
    {
        return TaskAction::Complete->value;
    }

    #[\Override]
    public function getLabel(): string
    {
        return TaskAction::Complete->label();
    }

    #[\Override]
    public function isAllowed(int $customerId, ?int $executorId, int $userId): bool
    {
        return $customerId === $userId && $executorId !== null;
    }
}
