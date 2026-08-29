<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\TaskFilterDto;
use \app\models\Task;

interface TaskRepositoryInterface
{
    /** @return Task[] */
    public function findNew(TaskFilterDto $filter): array;
}
