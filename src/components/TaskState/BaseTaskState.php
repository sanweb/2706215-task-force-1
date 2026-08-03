<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

abstract class BaseTaskState implements TaskStateInterface
{
    /**
     * Returns the status represented by the current state.
     */
    abstract public function getStatus(): TaskStatus;

    /**
     * Returns the actions available for the current status.
     *
     * @return list<TaskAction>
     */
    public function getAvailableActions(): array
    {
        return [];
    }

    /**
     * Returns the string identifier of the current status.
     */
    public function getStatusName(): string
    {
        return $this->getStatus()->value;
    }

    /**
     * Returns the human-readable label of the current status.
     */
    public function getStatusLabel(): string
    {
        return $this->getStatus()->label();
    }
}
