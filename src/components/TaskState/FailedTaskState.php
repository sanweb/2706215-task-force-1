<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Override;
use Sanweb\Taskforce\enum\TaskStatus;

class FailedTaskState extends BaseTaskState
{
    #[Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::Failed;
    }
}
