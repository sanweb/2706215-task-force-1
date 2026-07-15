<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

class NewTaskStatus extends BaseTaskStatus
{

    #[\Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::New;
    }

    #[\Override]
    public function getAvailableActions(): array
    {
        return [
            TaskAction::Cancel,
            TaskAction::Bid,
            TaskAction::Assign,
        ];
    }

    #[\Override]
    public function cancel(): TaskStatus
    {
        //$this->task->setStatus(new CanceledTaskStatus($this->task));
        //return $this->task->getStatus();
        return TaskStatus::Canceled;
    }

    #[\Override]
    public function bid(): TaskStatus
    {
        // this method does not change the current status
        return $this->getStatus();
    }

    #[\Override]
    public function assign(): TaskStatus
    {
        //$this->task->setStatus(new InProgressTaskStatus($this->task));
        //return $this->task->getStatus();
        return TaskStatus::InProgress;
    }
}
