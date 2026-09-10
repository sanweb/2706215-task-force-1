<?php

declare(strict_types=1);

namespace app\controllers;

use app\dto\TaskFilterDto;
use app\repositories\CategoryRepository;
use app\repositories\TaskRepository;
use app\requests\TaskFilterRequest;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TaskController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        mixed $id,
        mixed $module,
        private readonly TaskRepository $taskRepository,
        private readonly CategoryRepository $categoryRepository,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Displays the task list.
     */
    public function actionIndex(): string
    {
        $filterForm = new TaskFilterRequest();
        $filterForm->load(Yii::$app->request->queryParams);

        $filter = new TaskFilterDto();

        if ($filterForm->validate()) {
            $filter = $filterForm->toDto();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $this->taskRepository->findNewQuery($filter),
            'pagination' => [
                'pageSize' => (int) (Yii::$app->params['pagination']['tasksPageSize'] ?? 5),
                'pageSizeLimit' => false,
            ],
        ]);

        return $this->render('index', [
            'tasks' => $dataProvider->models,
            'pagination' => $dataProvider->pagination,
            'categories' => $this->categoryRepository->findAll(),
            'filterForm' => $filterForm,
        ]);
    }

    /**
     * Displays a single task.
     *
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $task = $this->taskRepository->findDetailsById($id);

        if ($task === null) {
            throw new NotFoundHttpException('Задание не найдено.');
        }

        return $this->render('view', [
            'task' => $task,
        ]);
    }
}
