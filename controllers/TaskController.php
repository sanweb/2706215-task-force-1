<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Task;
use Sanweb\Taskforce\enum\TaskStatus;
use yii\web\Controller;

class TaskController extends Controller
{
    public function actionIndex(): string
    {
        $tasks = Task::find()
            ->where(['status' => TaskStatus::New->value])
            ->orderBy(['created_at' => SORT_DESC])
            ->with(['category', 'city'])
            ->all();

        return $this->render('index', ['tasks' => $tasks]);
    }
}
