<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

/**
 * Base class for task actions (required by the specification).
 */
abstract class BaseTaskAction implements TaskActionInterface
{
    /**
     * Returns the string identifier of the action.
     */
    public function getName(): string
    {
        return $this->getAction()->value;
    }

    /**
     * Returns the human-readable action label.
     */
    public function getLabel(): string
    {
        return $this->getAction()->label();
    }

    /**
     * Returns the action type.
     */
    abstract public function getAction(): TaskAction;

    /**
     * Returns the task status after the action is performed.
     */
    abstract public function getNextStatus(): ?TaskStatus;

    /**
     * Checks whether the action is allowed for the user.
     */
    abstract public function isAllowed(
        Task $task,
        User $user,
    ): bool;

    /**
     * Performs action-specific changes to the task.
     *
     * @param array<string, mixed> $parameters
     */
    public function execute(
        Task $task,
        User $user,
        array $parameters = [],
    ): Task
    {
        return $task;
    }
}
