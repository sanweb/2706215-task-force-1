<?php

declare(strict_types=1);

namespace app\controllers;

use app\dto\PaginationDto;
use app\dto\TaskFilterDto;
use app\repositories\CategoryRepositoryInterface;
use app\repositories\TaskRepositoryInterface;
use app\requests\TaskFilterRequest;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TaskController extends Controller
{
    public function __construct(
        mixed $id,
        mixed $module,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $filterForm = new TaskFilterRequest();
        $filterForm->load(Yii::$app->request->queryParams);

        $filter = new TaskFilterDto();

        if ($filterForm->validate()) {
            $filter = new TaskFilterDto(
                categories: $filterForm->categories,
                isRemote: (bool) $filterForm->isRemote,
                hasNoBid: (bool) $filterForm->hasNoBid,
                createdAfter: $filterForm->period !== ''
                    ? (strtotime($filterForm->period) ?: null)
                    : null,
            );
        }

        $pagination = new PaginationDto(
            page: max(1, (int) Yii::$app->request->get('page', 1)),
            pageSize: (int) (Yii::$app->params['pagination']['tasksPageSize'] ?? PaginationDto::DEFAULT_PAGE_SIZE),
        );

        $result = $this->taskRepository->findNew($filter, $pagination);

        return $this->render('index', [
            'tasks' => $result->tasks,
            'pagination' => $result->pagination,
            'categories' => $this->categoryRepository->findAll(),
            'filterForm' => $filterForm
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $task = $this->taskRepository->findById($id);

        if ($task === null) {
            throw new NotFoundHttpException('Задание не найдено.');
        }

        return $this->render('view', [
            'task' => $task,
        ]);
    }
}
