<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\exception\TaskStatusException;
use Sanweb\Taskforce\interface\TaskStatusInterface;
use Sanweb\Taskforce\models\Task;

abstract class BaseTaskStatus implements TaskStatusInterface
{
    /**
     * Reference to the context object (Task)
     */
    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Default implementation for cancel action.
     *
     * By default, this action is not allowed in most states.
     * Only states that support this transition will override this method.
     *
     * @throws TaskStatusException
     */
    public function cancel(): TaskStatus
    {
        throw new TaskStatusException('Cannot cancel task in ' . $this->getStatusName() . ' status');
    }

    /**
     * Default implementation for bid action.
     *
     * By default, this action is not allowed in most states.
     * Only states that support this transition will override this method.
     *
     * @throws TaskStatusException
     */
    public function bid(): TaskStatus
    {
        throw new TaskStatusException('Cannot submit bid on task in ' . $this->getStatusName() . ' status');
    }

    /**
     * Default implementation for assign action.
     *
     * By default, this action is not allowed in most states.
     * Only states that support this transition will override this method.
     *
     * @throws TaskStatusException
     */
    public function assign(): TaskStatus
    {
        throw new TaskStatusException('Cannot assign executor on task in ' . $this->getStatusName() . ' status');
    }

    /**
     * Default implementation for complete action.
     *
     * By default, this action is not allowed in most states.
     * Only states that support this transition will override this method.
     *
     * @throws TaskStatusException
     */
    public function complete(): TaskStatus
    {
        throw new TaskStatusException('Cannot complete task in ' . $this->getStatusName() . ' status');
    }

    /**
     * Default implementation for refuse action.
     *
     * By default, this action is not allowed in most states.
     * Only states that support this transition will override this method.
     *
     * @throws TaskStatusException
     */
    public function refuse(): TaskStatus
    {
        throw new TaskStatusException('Cannot refuse task in ' . $this->getStatusName() . ' status');
    }

    /**
     * Default implementation for getting available actions.
     *
     * By default, returns empty array.
     * Only states that have available actions will override this method.
     *
     * @return array
     */
    public function getAvailableActions(): array
    {
        return [];
    }

    /**
     * Public method to get the task status name from its status.
     *
     * @return string The name of the current task status
     */
    public function getStatusName(): string
    {
        return $this->getStatus()->name;
    }

    public function getStatusValue(): string
    {
        return $this->getStatus()->value;
    }

    public function getStatusLabel(): string
    {
        return $this->getStatus()->label();
    }

    /**
     * Abstract method to get the task status.
     *
     * Each concrete task status must implement this method to return its status.
     *
     * @return TaskStatus The current task status
     */

    abstract public function getStatus(): TaskStatus;
}
