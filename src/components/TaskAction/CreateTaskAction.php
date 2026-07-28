<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;

/**
 * Creates the task.
 *
 * Artificial action required by the specification.
 * The task should be created separately.
 */
final class CreateTaskAction extends BaseTaskAction
{
    #[Override]
    public function getName(): string
    {
        return TaskAction::Create->value;
    }

    #[Override]
    public function getLabel(): string
    {
        return TaskAction::Create->label();
    }

    #[Override]
    public function isAllowed(int $customerId, ?int $executorId, int $userId): bool
    {
        return true;
    }
}
