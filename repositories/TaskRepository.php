<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\PaginationDto;
use app\dto\TaskFilterDto;
use app\dto\TaskSearchResultDto;
use app\models\Task;
use Sanweb\Taskforce\enum\TaskStatus;
use yii\data\Pagination;

final class TaskRepository
{
    /**
     * Finds new tasks matching the filter with pagination.
     */
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

    /**
     * Finds a task by ID.
     */
    public function findById(int $id): ?Task
    {
        return Task::findOne($id);
    }

    /**
     * Finds a task with the data required by the task details page.
     */
    public function findDetailsById(int $id): ?Task
    {
        return Task::find()
            ->where(['task.id' => $id])
            ->with([
                'category',
                'bids.user.executorStats',
                'bids.user.receivedReviews',
            ])
            ->one();
    }

    /**
     * Checks for an active task assigned to the executor by the customer.
     */
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
