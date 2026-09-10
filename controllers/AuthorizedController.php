<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AuthorizedController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return Yii::$app->response->redirect(['site/index']);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}
