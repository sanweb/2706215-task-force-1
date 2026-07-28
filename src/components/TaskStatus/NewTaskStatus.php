<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Override;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

class NewTaskStatus extends BaseTaskStatus
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

    #[Override]
    public function cancel(): TaskStatus
    {
        return TaskStatus::Canceled;
    }

    #[Override]
    public function bid(): TaskStatus
    {
        return $this->getStatus();
    }

    #[Override]
    public function assign(): TaskStatus
    {
        return TaskStatus::InProgress;
    }
}
