<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

class NewTaskState extends BaseTaskState
{

    #[Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::New;
    }

    #[Override]
    public function getAvailableActions(): array
    {
        return [
            TaskAction::Cancel,
            TaskAction::Bid,
            TaskAction::Assign,
        ];
    }
}
