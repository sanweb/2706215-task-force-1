<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\City;
use app\repositories\CityRepository;
use app\repositories\UserRepository;
use app\requests\UserSignupRequest;
use app\services\UserService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property mixed $taskRepository
 */
class UserController extends Controller
{
    public function __construct(
        mixed $id,
        mixed $module,
        private readonly UserRepository $userRepository,
        private readonly CityRepository $cityRepository,
        private readonly UserService $userService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Displays a single User model.
     *
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException('Исполнитель не найден.');
        }

        $canViewContacts = !$user->executorProfile?->hide_my_contacts;

        if (!$canViewContacts && !Yii::$app->user->isGuest) {
            $canViewContacts = $this->taskRepository->hasActiveTaskWithExecutor(
                (int) Yii::$app->user->id,
                $user->id
            );
        }

        return $this->render('view', [
            'user' => $user,
            'canViewContacts' => $canViewContacts,
        ]);
    }

    public function actionSignup()
    {
        $signupForm = new UserSignupRequest();

        if ($signupForm->load($this->request->post()) && $signupForm->validate()) {
            $user = $this->userService->signup($signupForm->toDto());

            Yii::$app->user->login($user);

            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $signupForm,
            'cities' => $this->cityRepository->findAllForSelect(),
        ]);
    }
}
