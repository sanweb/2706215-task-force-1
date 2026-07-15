<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\interface\TaskStatusInterface;
use Sanweb\Taskforce\models\Task;

final class TaskStatusFactory
{
    public static function create(
        TaskStatus $status,
        Task $task
    ): TaskStatusInterface
    {
        return match ($status) {
            TaskStatus::New => new NewTaskStatus($task),
            TaskStatus::Canceled => new CanceledTaskStatus($task),
            TaskStatus::InProgress => new InProgressTaskStatus($task),
            TaskStatus::Completed => new CompletedTaskStatus($task),
            TaskStatus::Failed => new FailedTaskStatus($task),
        };
    }
}
