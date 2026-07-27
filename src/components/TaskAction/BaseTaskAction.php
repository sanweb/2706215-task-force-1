<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskAction;

use Sanweb\Taskforce\interface\TaskActionInterface;

/**
 * Base class for task actions (required by the specification).
 */
abstract class BaseTaskAction implements TaskActionInterface
{
    // Method declarations come from the interface.
    /*
    abstract public function getName(): string;
    abstract public function getLabel(): string;
    abstract public function isAllowed(
        int $customerId,
        ?int $executorId,
        int $userId
    ): bool;
    */
}
