<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;

interface TaskStateInterface
{
    /**
     * Returns the status represented by this state.
     */
    public function getStatus(): TaskStatus;

    /**
     * Returns the actions available for this status.
     *
     * @return list<TaskAction>
     */
    public function getAvailableActions(): array;

    /**
     * Returns the string identifier of the status.
     */
    public function getStatusName(): string;

    /**
     * Returns the human-readable status label.
     */
    public function getStatusLabel(): string;
}
