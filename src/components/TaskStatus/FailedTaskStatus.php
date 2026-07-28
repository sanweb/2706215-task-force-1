<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Override;
use Sanweb\Taskforce\enum\TaskStatus;

class FailedTaskStatus extends BaseTaskStatus
{
    #[Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::Failed;
    }
}
