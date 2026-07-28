<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Override;
use Sanweb\Taskforce\enum\TaskAction;

/**
 * Makes a bid on the task.
 */
final class BidTaskAction extends BaseTaskAction
{
    #[Override]
    public function getName(): string
    {
        return TaskAction::Bid->value;
    }

    #[Override]
    public function getLabel(): string
    {
        return TaskAction::Bid->label();
    }

    #[Override]
    public function isAllowed(int $customerId, ?int $executorId, int $userId): bool
    {
        return $customerId !== $userId && $executorId === null;
    }
}
