<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\services;

use Sanweb\Taskforce\components\TaskAction\TaskActionFactory;
use Sanweb\Taskforce\components\TaskState\TaskStateMachine;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\exception\TaskActionException;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

final readonly class TaskService
{
    public function __construct(
        private TaskStateMachine $stateMachine,
    ) {}

    public function performAction(
        Task $task,
        TaskAction $actionType,
        User $user,
        array $parameters = [],
    ) {
        if (!$this->stateMachine->isActionAvailable(
            $task,
            $actionType
        )) {
            throw new TaskActionException(
                'Action is unavailable for the current status.',
            );
        }

        $action = TaskActionFactory::create($actionType);

        if (!$action->isAllowed($task, $user)) {
            throw new TaskActionException(
                'Action is unavailable for the current user.',
            );
        }

        $task = $action->execute($task, $user, $parameters);
        $nextStatus = $action->getNextStatus();

        if (
            $nextStatus !== null
            && $nextStatus !== $task->getStatus()
        ) {
            $task = $task->withStatus($nextStatus);
        }

        return $task;
    }
}
