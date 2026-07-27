<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\interface;

interface TaskActionInterface
{
    /**
     * Returns action internal name.
     */
    public function getName(): string;

    /**
     * Returns action label.
     */
    public function getLabel(): string;

    /**
     * Checks whether the action is allowed for the user.
     */
    public function isAllowed(
        int $customerId,
        ?int $executorId,
        int $userId
    ): bool;
}
