<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\Task $task */
?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= Html::encode($task->title) ?></h3>
        <p class="price price--big"><?= Yii::$app->formatter->asCurrency($task->budget) ?></p>
    </div>
    <p class="task-description"><?= Html::encode($task->description) ?></p>
    <!-- TODO: Implement action buttons and map -->
    <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
    <!--
    <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
    <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
    -->
    <div class="task-map">
        <img class="map" src="/img/map.png" width="725" height="346" alt="Новый арбат, 23, к. 1">
        <p class="map-address town">Москва</p>
        <p class="map-address">Новый арбат, 23, к. 1</p>
    </div>

    <h4 class="head-regular">Отклики на задание</h4>

    <?php foreach ($task->bids as $bid): ?>
        <div class="response-card">
            <img class="customer-photo" src="<?= $bid->user->avatar ?? '/img/avatars/default.png' ?>" width="146" height="156" alt="Фото исполнителя">
            <div class="feedback-wrapper">
                <a href="<?= Url::to(['user/view', 'id' => $bid->user_id]) ?>" class="link link--block link--big"><?= Html::encode($bid->user->name) ?></a>
                <div class="response-wrapper">
                    <!-- TODO: Implement dynamic reviews -->
                    <div class="stars-rating small">
                        <span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span>
                    </div>
                    <p class="reviews"><?= count($bid->user->receivedReviews) ?>  отзывов<!--отзыва--></p>
                </div>
                <p class="response-message"><?= Html::encode($bid->comment) ?></p>
            </div>
            <div class="feedback-wrapper">
                <p class="info-text"><span class="current-time"><?= Yii::$app->formatter->asRelativeTime($bid->created_at) ?></span><!--назад?--></p>
                <p class="price price--small"><?= Yii::$app->formatter->asCurrency($bid->price) ?></p>
            </div>
            <div class="button-popup">
                <!-- TODO: Implement action buttons -->
                <a href="#" class="button button--blue button--small">Принять</a>
                <a href="#" class="button button--orange button--small">Отказать</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><?= Html::encode($task->category->name) ?></dd>
            <dt>Дата публикации</dt>
            <dd><?= Yii::$app->formatter->asRelativeTime($task->created_at) ?></dd>
            <dt>Срок выполнения</dt>
            <!-- ? Expiration date has no time component. -->
            <dd><?= Yii::$app->formatter->asDatetime($task->expire_date, 'd MMMM, HH:mm') ?></dd>
            <dt>Статус</dt>
            <dd><?= Html::encode($task->statusLabel) ?></dd>
        </dl>
    </div>
    <!-- TODO: Implement attachments -->
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">my_picture.jpg</a>
                <p class="file-size">356 Кб</p>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">information.docx</a>
                <p class="file-size">12 Кб</p>
            </li>
        </ul>
    </div>
</div>
