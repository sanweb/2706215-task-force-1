<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Override;
use Sanweb\Taskforce\enum\TaskStatus;

class CanceledTaskState extends BaseTaskState
{
    #[Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::Canceled;
    }
}
