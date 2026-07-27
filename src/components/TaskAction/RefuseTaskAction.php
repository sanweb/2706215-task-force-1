<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\enum\TaskAction;

/**
 * Refuses the task.
 */
final class RefuseTaskAction extends BaseTaskAction
{
    #[\Override]
    public function getName(): string
    {
        return TaskAction::Refuse->value;
    }

    #[\Override]
    public function getLabel(): string
    {
        return TaskAction::Refuse->label();
    }

    #[\Override]
    public function isAllowed(int $customerId, ?int $executorId, int $userId): bool
    {
        return $executorId !== null && $executorId === $userId;
    }
}
