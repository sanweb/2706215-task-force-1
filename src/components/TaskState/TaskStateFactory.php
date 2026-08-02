<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Sanweb\Taskforce\enum\TaskStatus;

final class TaskStateFactory
{
    public static function create(TaskStatus $status): TaskStateInterface
    {
        return match ($status) {
            TaskStatus::New => new NewTaskState(),
            TaskStatus::Canceled => new CanceledTaskState(),
            TaskStatus::InProgress => new InProgressTaskState(),
            TaskStatus::Completed => new CompletedTaskState(),
            TaskStatus::Failed => new FailedTaskState(),
        };
    }
}
