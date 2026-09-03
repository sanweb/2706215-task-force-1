<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\PaginationDto;
use app\dto\TaskFilterDto;
use app\dto\TaskSearchResultDto;
use app\models\Task;

interface TaskRepositoryInterface
{
    public function findNew(TaskFilterDto $filter, PaginationDto $pagination): TaskSearchResultDto;
    public function findById(int $id): ?Task;
    public function hasActiveTaskWithExecutor(int $customerId, int $executorId): bool;
}
