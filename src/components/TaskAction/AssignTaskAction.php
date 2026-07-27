<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\enum\TaskAction;

/**
 * Assigns an executor to the task.
 */
final class AssignTaskAction extends BaseTaskAction
{
    #[\Override]
    public function getName(): string
    {
        return TaskAction::Assign->value;
    }

    #[\Override]
    public function getLabel(): string
    {
        return TaskAction::Assign->label();
    }

    #[\Override]
    public function isAllowed(int $customerId, ?int $executorId, int $userId): bool
    {
        return $customerId === $userId && $executorId === null;
    }
}
