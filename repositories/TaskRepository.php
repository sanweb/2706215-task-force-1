<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\TaskCreateDto;
use app\dto\TaskFilterDto;
use app\models\Task;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\exception\TaskCreateException;
use Yii;
use yii\db\ActiveQuery;

final class TaskRepository
{
    /**
     * Builds a query for new tasks matching the filter.
     *
     * @return ActiveQuery<Task>
     */
    public function findNewQuery(TaskFilterDto $filter): ActiveQuery
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

        return $query;
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

    /**
     * Creates and persists a task for the authenticated user.
     *
     * @throws TaskCreateException
     */
    public function create(TaskCreateDto $dto): Task
    {
        $task = new Task();
        $task->customer_id = (int) Yii::$app->user->id;
        $task->category_id = $dto->categoryId;
        $task->title = $dto->title;
        $task->description = $dto->description;
        $task->budget = $dto->budget;
        $task->expire_date = $dto->expireDate;
        $task->location = $dto->location;
        $task->city_id = $dto->cityId;

        if (!$task->save()) {
            throw new TaskCreateException('Не удалось создать задание.');
        }

        return $task;
    }
}
