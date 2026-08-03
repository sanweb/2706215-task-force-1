<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\models\User;

interface TaskActionInterface
{
    /**
     * Returns the action type.
     */
    public function getAction(): TaskAction;

    /**
     * Returns the string identifier of the action.
     */
    public function getName(): string;

    /**
     * Returns the human-readable action label.
     */
    public function getLabel(): string;

    /**
     * Returns the task status after the action is performed.
     */
    public function getNextStatus(): ?TaskStatus;

    /**
     * Checks whether the action is allowed for the user.
     */
    public function isAllowed(
        Task $task,
        User $user,
    ): bool;

    /**
     * Performs action-specific changes to the task.
     *
     * The task status is changed separately by TaskService.
     *
     * @param array<string, mixed> $parameters
     */
    public function execute(
        Task $task,
        User $user,
        array $parameters = [],
    ): Task;
}
