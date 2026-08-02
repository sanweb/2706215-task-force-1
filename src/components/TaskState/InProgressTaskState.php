<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

class InProgressTaskState extends BaseTaskState
{

    #[Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::InProgress;
    }

    #[Override]
    public function getAvailableActions(): array
    {
        return [
            TaskAction::Complete,
            TaskAction::Refuse,
        ];
    }
}
