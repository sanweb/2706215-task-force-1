<?php

declare(strict_types=1);

namespace app\dto;

use app\models\Task;
use yii\data\Pagination;

final readonly class TaskSearchResultDto
{
    /** @param Task[] $tasks */
    public function __construct(
        public array $tasks,
        public Pagination $pagination,
    ) {}
}
