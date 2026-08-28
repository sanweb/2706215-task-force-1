<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Category;
use app\models\Task;
use app\requests\TaskFilterRequest;
use Sanweb\Taskforce\enum\TaskStatus;
use Yii;
use yii\web\Controller;

class TaskController extends Controller
{
    public function actionIndex(): string
    {
        $filterForm = new TaskFilterRequest();
        $filterForm->load(Yii::$app->request->queryParams);

        $query = Task::find()
            ->where(['task.status' => TaskStatus::New->value])
            ->orderBy(['task.created_at' => SORT_DESC])
            ->with(['category', 'city']);

        if ($filterForm->validate()) {
            $query->andFilterWhere(['in', 'category_id', $filterForm->categories]);

            if ($filterForm->isRemote) {
                $query->andWhere(['city_id' => null]);
            }

            if ($filterForm->hasNoBid) {
                $query->joinWith('bids', false)
                    ->andWhere(['bid.id' => null]);
            }

            if ($filterForm->period !== '') {
                $timestamp = strtotime($filterForm->period);

                if ($timestamp !== false) {
                    $query->andWhere(['>=', 'task.created_at', $timestamp]);
                }
            }
        }

        $tasks = $query->all();
        $categories = Category::find()->all();

        return $this->render('index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'filterForm' => $filterForm
        ]);
    }
}
