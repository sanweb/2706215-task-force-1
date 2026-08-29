<?php

declare(strict_types=1);

namespace app\repositories;

use app\dto\TaskFilterDto;
use app\models\Task;
use Override;
use Sanweb\Taskforce\enum\TaskStatus;

final class TaskRepository implements TaskRepositoryInterface
{
    #[Override]
    public function findNew(TaskFilterDto $filter): array
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

        return $query->all();
    }
}
