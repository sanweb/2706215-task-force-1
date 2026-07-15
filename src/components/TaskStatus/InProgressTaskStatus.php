<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

class InProgressTaskStatus extends BaseTaskStatus
{

    #[\Override]
    public function getStatus(): TaskStatus
    {
        return TaskStatus::InProgress;
    }

    #[\Override]
    public function getAvailableActions(): array
    {
        return [
            TaskAction::Complete,
            TaskAction::Refuse,
        ];
    }

    #[\Override]
    public function complete(): TaskStatus
    {
        //$this->task->setStatus(new CompletedTaskStatus($this->task));
        //return $this->task->getStatus();
        return TaskStatus::Completed;
    }

    #[\Override]
    public function refuse(): TaskStatus
    {
        //$this->task->setStatus(new FailedTaskStatus($this->task));
        //return $this->task->getStatus();
        return TaskStatus::Failed;
    }

}
