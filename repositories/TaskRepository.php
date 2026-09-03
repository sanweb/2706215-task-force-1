<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\PaginationDto;
use app\dto\TaskFilterDto;
use app\dto\TaskSearchResultDto;
use app\models\Task;
use Override;
use Sanweb\Taskforce\enum\TaskStatus;
use yii\data\Pagination;

final class TaskRepository implements TaskRepositoryInterface
{
    #[Override]
    public function findNew(TaskFilterDto $filter, PaginationDto $pagination): TaskSearchResultDto
    {
        $query = Task::find()
            ->where(['task.status' => TaskStatus::New->value])
            ->orderBy(['task.created_at' => SORT_DESC])
            ->with(['category', 'city']);

        $query->andFilterWhere(['in', 'task.category_id', $filter->categories]);

        if ($filter->isRemote) {
            $query->andWhere(['task.city_id' => null]);
        }

        if ($filter->hasNoBid) {
            $query->joinWith('bids', false)->andWhere(['bid.id' => null]);
        }

        if ($filter->createdAfter !== null) {
            $query->andWhere(['>=', 'task.created_at', $filter->createdAfter]);
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => $pagination->pageSize,
            'page' => $pagination->page - 1,
        ]);

        $tasks = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return new TaskSearchResultDto(
            tasks: $tasks,
            pagination: $pagination,
        );
    }

    #[Override]
    public function findById(int $id): ?Task
    {
        return Task::findOne($id);
    }

    #[Override]
    public function hasActiveTaskWithExecutor(int $customerId, int $executorId): bool
    {
        return Task::find()
            ->where([
                'customer_id' => $customerId,
                'executor_id' => $executorId,
                'status' => TaskStatus::InProgress->value,
            ])
            ->exists();
    }
}
