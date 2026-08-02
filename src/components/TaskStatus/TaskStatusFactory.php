<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Sanweb\Taskforce\enum\TaskStatus;

final class TaskStatusFactory
{
    public static function create(TaskStatus $status): TaskStatusInterface
    {
        return match ($status) {
            TaskStatus::New => new NewTaskStatus(),
            TaskStatus::Canceled => new CanceledTaskStatus(),
            TaskStatus::InProgress => new InProgressTaskStatus(),
            TaskStatus::Completed => new CompletedTaskStatus(),
            TaskStatus::Failed => new FailedTaskStatus(),
        };
    }
}
