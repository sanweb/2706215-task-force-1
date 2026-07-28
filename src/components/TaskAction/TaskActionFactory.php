<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\interface\TaskActionInterface;

final class TaskActionFactory
{
    public static function create(TaskAction $action): TaskActionInterface
    {
        return match ($action) {
            TaskAction::Cancel => new CancelTaskAction(),
            TaskAction::Bid => new BidTaskAction(),
            TaskAction::Assign => new AssignTaskAction(),
            TaskAction::Complete => new CompleteTaskAction(),
            TaskAction::Refuse => new RefuseTaskAction(),
        };
    }
}
