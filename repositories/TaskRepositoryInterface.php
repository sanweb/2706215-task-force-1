<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\PaginationDto;
use app\dto\TaskFilterDto;
use app\dto\TaskSearchResultDto;

interface TaskRepositoryInterface
{
    public function findNew(TaskFilterDto $filter, PaginationDto $pagination): TaskSearchResultDto;
}
