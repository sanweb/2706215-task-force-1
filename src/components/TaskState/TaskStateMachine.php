<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskState;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\models\Task;

final readonly class TaskStateMachine
{
    public function getAvailableActions(Task $task): array
    {
        $state = TaskStateFactory::create($task->getStatus());

        return $state->getAvailableActions();
    }

    public function isActionAvailable(Task $task, TaskAction $action): bool
    {
        return in_array($action, $this->getAvailableActions($task), true);
    }
}
