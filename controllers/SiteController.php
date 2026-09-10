<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\requests\UserLoginRequest;
use app\services\AuthService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        mixed $id,
        mixed $module,
        private readonly AuthService $authService,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        if (!Yii::$app->user->isGuest) {
            $this->redirect(['task/index']);
        }

        $this->layout = 'landing';
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $loginRequest = new UserLoginRequest();

        if ($loginRequest->load($this->request->post()) && $loginRequest->validate()) {
            $user = $this->authService->authenticate($loginRequest->toDto());

            if ($user !== null && Yii::$app->user->login($user)) {
                return $this->goBack();
            }

            $loginRequest->addError('password', 'Неверный email или пароль.');
        }

        $loginRequest->password = '';
        return $this->render('login', ['model' => $loginRequest]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
