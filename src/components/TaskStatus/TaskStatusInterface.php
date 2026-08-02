<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\TaskStatus;

use Sanweb\Taskforce\enum\TaskStatus;

interface TaskStatusInterface
{
    public function cancel(): TaskStatus;
    public function bid(): TaskStatus;
    public function assign(): TaskStatus;
    public function complete(): TaskStatus;
    public function refuse(): TaskStatus;
    public function getAvailableActions(): array;
    public function getStatusName(): string;
}
