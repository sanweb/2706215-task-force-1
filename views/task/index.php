<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var \app\models\Task[] $tasks */
/** @var \app\models\Category[] $categories */
/** @var \app\requests\TaskFilterRequest $filterForm */
?>

<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($tasks as $task): ?>
        <div class="task-card">
            <div class="header-task">
                <a href="#" class="link link--block link--big"><?= Html::encode($task->title) ?></a>
                <p class="price price--task"><?= Yii::$app->formatter->asCurrency($task->budget) ?></p>
            </div>
            <p class="info-text"><span class="current-time"><?= Yii::$app->formatter->asRelativeTime($task->created_at) ?></span><!--назад?--></p>
            <p class="task-text"><?= Html::encode($task->description) ?></p>
            <div class="footer-task">
                <?php if ($task->city): ?>
                    <p class="info-text town-text">
                        <?= Html::encode($task->city->name) ?>
                        <?= Html::encode($task->location) ?>
                    </p>
                <?php endif; ?>
                <?php if ($task->category): ?>
                    <p class="info-text category-text"><?= Html::encode($task->category->name) ?></p>
                <?php endif; ?>
                <a href="#" class="button button--black">Смотреть Задание</a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php // TODO: Implement pagination
    ?>
    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">1</a>
            </li>
            <li class="pagination-item pagination-item--active">
                <a href="#" class="link link--page">2</a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">3</a>
            </li>
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
        </ul>
    </div>
</div>
<div class="right-column">
    <div class="right-card black">
        <div class="search-form">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['task/index'],
            ]); ?>

            <h4 class="head-card">Категории</h4>

            <div class="form-group">
                <div class="checkbox-wrapper">
                    <?= $form->field($filterForm, 'categories', [
                        'template' => '{input}',
                        'options' => ['tag' => false],
                    ])->checkboxList(
                        ArrayHelper::map($categories, 'id', 'name'),
                        [
                            'item' => function ($index, $label, $name, $checked, $value): string {
                                $id = 'category-' . $value;

                                return Html::label(
                                    Html::checkbox(
                                        $name,
                                        $checked,
                                        ['value' => $value, 'id' => $id,]
                                    ) . ' ' . Html::encode($label),
                                    $id,
                                    ['class' => 'control-label']
                                );
                            },
                        ]
                    ) ?>
                </div>
            </div>

            <h4 class="head-card">Дополнительно</h4>

            <?= $form->field($filterForm, 'isRemote', [
                'template' => '{input}',
                'options' => ['tag' => false],
            ])->checkbox([
                'label' => 'Удалённая работа',
                'labelOptions' => ['class' => 'control-label'],
            ]) ?>

            <?= $form->field($filterForm, 'hasNoBid', [
                'template' => '{input}',
                'options' => ['tag' => false],
            ])->checkbox([
                'label' => 'Без откликов',
                'labelOptions' => ['class' => 'control-label'],
            ]) ?>

            <h4 class="head-card">Период</h4>

            <div class="form-group">
                <?= $form->field($filterForm, 'period', [
                    'template' => '{input}',
                    'options' => ['tag' => false],
                ])->dropDownList(
                    $filterForm::PERIODS,
                    [
                        'prompt' => 'Выберите период',
                        'id' => 'period-value',
                    ]
                ) ?>
            </div>

            <?= Html::submitButton('Искать', ['class' => 'button button--blue']) ?>

            <?php $form->end(); ?>
        </div>
    </div>
</div>