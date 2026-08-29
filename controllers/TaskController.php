<?php

declare(strict_types=1);

namespace app\controllers;

use app\dto\TaskFilterDto;
use app\repositories\CategoryRepositoryInterface;
use app\repositories\TaskRepositoryInterface;
use app\requests\TaskFilterRequest;
use Yii;
use yii\web\Controller;

class TaskController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        $config = []
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

        return $this->render('index', [
            'tasks' => $this->taskRepository->findNew($filter),
            'categories' => $this->categoryRepository->findAll(),
            'filterForm' => $filterForm
        ]);
    }
}
